<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ChatBotApiController extends Controller
{


    // **************** Luồng xử lý chính trong ChatBotApiController tóm tắt ngắn gọn như sau **************
    // Nhận tin nhắn từ user:
    // Lấy message từ request
    // Xác định user ID
    // Xử lý các trường hợp đặc biệt:
    // Kiểm tra yêu cầu "xem thêm sản phẩm" → trả về sản phẩm mới nếu có
    // Xử lý các câu hỏi đặc biệt (giờ mở cửa, size, khuyến mãi...) → trả về câu trả lời có sẵn
    // Xử lý tin nhắn thông thường:
    // Lấy lịch sử chat từ Redis
    // Tóm tắt lịch sử nếu quá dài (>50 tin)
    // Xây dựng prompt với context sản phẩm (nếu có)
    // Gửi yêu cầu đến AI (Ollama)
    // Xử lý phản hồi từ AI:
    // Lưu tin nhắn mới vào lịch sử Redis
    // Trả về kết quả cho người dùng
    // Xử lý sản phẩm
    // Phát hiện từ khóa sản phẩm → tìm kiếm sản phẩm phù hợp
    // Lưu context sản phẩm vào Redis
    // Trả về danh sách sản phẩm dạng card
    // Các chức năng chính:
    // Hỗ trợ đa tương tác (chat, sản phẩm)
    // Duy trì ngữ cảnh cuộc hội thoại
    // Tự động tóm tắt khi hội thoại dài

    //  **************Flow xử lý tin nhắn***************
    //  User Message -> Special Cases Check  -> [Nếu không match] → Lấy lịch sử từ Redis
    //      -> Check threshold → [Nếu > 50] → Tóm tắt -> Xây dựng Multi-turn Prompt
    //        -> call api tới Ollama AI -> Xử lý Response -> Lưu vào Redis(list string) ->Return JSON Response


    // https://res.cloudinary.com/dc2zvj1u4/image/upload/v1748404290/ao/file_u0eqqq.jpg --size guide


    // PROMPT MẶC ĐỊNH
    protected $defaultPrompt = "
        Bạn là một trợ lý chatbot thông minh cho TST Fashion Shop - cửa hàng thời trang online tại Việt Nam. Hãy luôn thân thiện, chuyên nghiệp và hữu ích.
        ### THÔNG TIN CỬA HÀNG:
        - Địa chỉ chi nhánh Cần Thơ: 3/2, Xuân Khánh, Cần Thơ
        - Chính sách đổi trả: 30 ngày
        - Phương thức thanh toán: COD, VNPay, Momo, ZaloPay
        - Size áo/quần: XS, S, M, L, XL, XXL

        ### HƯỚNG DẪN PHẢN HỒI:
        1. Khi hỏi về sản phẩm:
        - Sử dụng thông tin sản phẩm có sẵn trong context nếu có
        - Cung cấp thông tin chi tiết: chất liệu, size, màu sắc, giá
        - So sánh, tư vấn dựa trên sản phẩm đã biết
        - Kèm link sản phẩm khi có thể, nếu sản phẩm có link hãy gửi kèm thẻ <a> để truy cập thay vì text

        2. Tương tác thông minh:
        - Khi khách hỏi 'cái nào đẹp hơn' → So sánh các sản phẩm đã show
        - Khi hỏi về giá → Tham khảo giá các sản phẩm trong context
        - Khi hỏi về size → Dựa vào sản phẩm đã đề cập
        - Gợi ý combo, phối đồ từ các sản phẩm có sẵn

        ### LIÊN KẾT QUAN TRỌNG:
        - Trang liên hệ: <a href='http://127.0.0.1:8000/contact'>Contacts</a>
        - Blog thời trang: <a href='http://127.0.0.1:8000/blog'>Blog</a>
        - Cửa hàng: <a href='http://127.0.0.1:8000/shop'>Shop</a>
        - Hướng dẫn chọn size: <a href='https://res.cloudinary.com/dc2zvj1u4/image/upload/v1748404290/ao/file_u0eqqq.jpg'>Hướng dẫn chọn size</a>
        ### QUY TẮC HIỂN THỊ:
        - Khi cần hiển thị ảnh, sử dụng thẻ HTML <img> với style phù hợp
        - Đảm bảo ảnh responsive: style='max-width: 100%; height: auto;'
        - Thêm border-radius và margin để ảnh đẹp hơn
        ";


    // Hàm xử lý gửi tin nhắn
    public function sendMessage(Request $request)
    {

        try {
            // KEY USER CÓ THỂ THAY ĐỔI BẰNG AUTH
            $userId = 'user_gemma3_newway';
            $userMessage = $request->input('message');
            // KEY HISTORY_CHAT TRONG REDIS
            $historyKey = "chat_history:$userId";
            // KEY PRODUCT_CONTEXT TRONG REDIS
            $productContextKey = "product_context:$userId";
            $maxMessages = 50; // GIỚ HẠN TIN NHẮN
            $summarizeThreshold = 50;

            // Xử lý hỏi thêm
            $moreProductsResponse = $this->handleMoreProductsRequest($userMessage, $userId);
            if ($moreProductsResponse) {
                return response()->json([
                    'reply_data' => $moreProductsResponse,
                    'reply' => $moreProductsResponse['content'] ?? $moreProductsResponse['message'] ?? ''
                ]);
            }

            // Kiểm tra special cases trước
            $specialResponse = $this->handleSpecialCases($userMessage, $userId);
            if ($specialResponse) {
                return response()->json([
                    'reply_data' => $specialResponse,
                    'reply' => $specialResponse['content'] ?? $specialResponse['message'] ?? ''
                ]);
            }

            // Lấy và xử lý lịch sử chat vào redis
            $historyRaw = Redis::lrange($historyKey, 0, -1);
            $history = array_map('json_decode', $historyRaw);

            // Tóm tắt nếu cần
            if (count($history) >= $summarizeThreshold) {
                $toSummarize = array_slice($history, 0, 40);
                $summaryText = $this->summarizeHistory($toSummarize);

                if ($summaryText) {
                    $history = array_slice($history, 40);
                    array_unshift($history, (object)[
                        'role' => 'system',
                        'message' => $summaryText
                    ]);

                    Redis::del($historyKey);
                    foreach ($history as $item) {
                        Redis::rpush($historyKey, json_encode($item));
                    }
                }
            }

            // Lấy 20 tin nhắn cuối
            $recentRaw = Redis::lrange($historyKey, -20, -1);
            $recentHistory = array_map('json_decode', $recentRaw);

            // Xây dựng system prompt với product context
            $fullSystemPrompt = $this->buildSystemPromptWithProductContext($userId);

            // Xây dựng chat prompt
            $chatPrompt = $this->buildChatPrompt($recentHistory, $fullSystemPrompt, $userMessage);

            // Gửi tới AI
            $payload = [
                'model' => 'gemma3:4b',
                'prompt' => $chatPrompt,
                'stream' => false
            ];

            $response = Http::timeout(60)->post('http://localhost:11434/api/generate', $payload);

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to connect to OLLama.'], 500);
            }

            $data = $response->json();
            $replyRaw = $data['response'] ?? '[Không có phản hồi từ AI]';
            $reply = preg_replace('/^ASSISTANT:\s*/i', '', $replyRaw);
            // xử lý ảnh
            $reply = $this->processContentWithImages($reply);


            // Lưu tin nhắn vào history
            Redis::rpush($historyKey, json_encode(['role' => 'user', 'message' => $userMessage]));
            Redis::rpush($historyKey, json_encode(['role' => 'assistant', 'message' => $reply]));
            Redis::ltrim($historyKey, -$maxMessages, -1);
            Redis::expire($historyKey, 60 * 60 * 24);

            return response()->json(['reply' => $reply]);
        } catch (Exception $e) {
            Log::error('Chatbot API Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            // Phản hồi thân thiện với người dùng
            return response()->json([
                'error' => 'Xin lỗi, đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
                'technical_message' => env('APP_DEBUG') ? $e->getMessage() : null,
                'reply' => 'Xin lỗi, hiện tại hệ thống đang gặp sự cố. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ nếu sự cố tiếp diễn.'
            ], 500);
        }
    }


    // Xử lý hỏi thêm sản phẩm
    protected function handleMoreProductsRequest(string $message, string $userId): ?array
    {
        $message = mb_strtolower(trim($message));

        // Kiểm tra các từ khóa yêu cầu xem thêm
        $moreKeywords = ['xem thêm', 'mẫu khác', 'còn không', 'khác đi', 'khác không', 'nữa không', 'nữa đi'];
        $isMoreRequest = false;

        foreach ($moreKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                $isMoreRequest = true;
                break;
            }
        }

        if (!$isMoreRequest) {
            return null;
        }

        // Lấy context sản phẩm trước đó từ Redis
        $productContextKey = "product_context:$userId";
        $contextRaw = Redis::lrange($productContextKey, 0, -1);

        if (empty($contextRaw)) {
            return null;
        }

        // Lấy sản phẩm cuối cùng được thảo luận
        $lastProduct = json_decode(end($contextRaw));
        $lastQuery = $lastProduct->query ?? '';

        // Nếu không có query trước đó thì không xử lý
        if (empty($lastQuery)) {
            return null;
        }

        // Phát hiện lại từ khóa sản phẩm từ query trước đó
        $productKeywords = $this->detectProductKeywords($lastQuery);

        if (empty($productKeywords)) {
            return null;
        }

        // Lấy thêm sản phẩm tương tự (tăng limit lên để lấy nhiều hơn)
        $products = $this->getProductRecommendations(
            $productKeywords['category'],
            $productKeywords['keywords'],
            10, // Tăng số lượng sản phẩm lấy ra
            5   // Offset bằng số sản phẩm đã hiển thị trước đó
        );

        if (!empty($products)) {
            // Lưu sản phẩm mới vào context (ghi đè lên cũ)
            $this->saveProductsToContext($userId, $products, $lastQuery);

            return $this->formatProductResponse($products, true);
        }

        return [
            'type' => 'text',
            'content' => "Hiện mình không tìm thấy thêm sản phẩm nào tương tự. Bạn có muốn xem sản phẩm khác không ạ?"
        ];
    }

    // Hàm xây dựng system prompt với product context
    protected function buildSystemPromptWithProductContext(string $userId): string
    {
        $productContextKey = "product_context:$userId";
        $productContextRaw = Redis::lrange($productContextKey, 0, -1);

        $fullSystemPrompt = $this->defaultPrompt;

        if (!empty($productContextRaw)) {
            $productContext = array_map('json_decode', $productContextRaw);
            $contextText = "\n\n### SAN PHẨM ĐÃ THẢO LUẬN TRONG PHIÊN:\n";

            foreach ($productContext as $item) {
                $contextText .= "- {$item->name}: {$item->price} - {$item->link}\n";
                if (!empty($item->details)) {
                    $contextText .= "  Chi tiết: {$item->details}\n";
                }
            }
            $contextText .= "\nHãy sử dụng thông tin này để tư vấn, so sánh và gợi ý cho khách hàng.\n";

            $fullSystemPrompt .= $contextText;
        }

        return $fullSystemPrompt;
    }

    // Hàm xây dựng chat prompt
    protected function buildChatPrompt(array $recentHistory, string $systemPrompt, string $userMessage): string
    {
        $systemMsg = '';
        $chatPrompt = '';

        foreach ($recentHistory as $item) {
            if ($item->role === 'system') {
                $systemMsg .= (!empty($systemMsg) ? "\n" : '') . $item->message;
                continue;
            }

            if (in_array($item->role, ['user', 'assistant'])) {
                $role = strtoupper($item->role);
                $message = $item->message;
                $chatPrompt .= "$role: $message\n";
            }
        }

        $fullSystemPrompt = $systemPrompt;
        if (!empty($systemMsg)) {
            $fullSystemPrompt .= "\nTóm tắt lịch sử chat trước đây: " . $systemMsg;
        }

        $chatPrompt = "SYSTEM: $fullSystemPrompt\n" . $chatPrompt;
        $chatPrompt .= "USER: $userMessage\nASSISTANT:";

        return $chatPrompt;
    }

    // Hàm xử lý trường hợp đặc biệt
    protected function handleSpecialCases(string $message, string $userId): ?array
    {
        $message = mb_strtolower(trim($message));

        // Hỏi về giờ mở cửa
        if (str_contains($message, 'giờ mở cửa') || str_contains($message, 'thời gian làm việc')) {
            return ['type' => 'text', 'content' => "Cửa hàng mở cửa từ 8:00 - 22:00 hàng ngày."];
        }

        // Hỏi về chính sách vận chuyển
        if (str_contains($message, 'phí vận chuyển') || str_contains($message, 'ship hàng')) {
            return ['type' => 'text', 'content' => "Hiện tại chúng tôi miễn phí vận chuyển cho đơn hàng từ 500.000đ trở lên. Đơn dưới 500.000đ phí ship là 25.000đ."];
        }

        if (str_contains($message, 'khuyến mãi') || str_contains($message, 'giảm giá') || str_contains($message, 'sale')) {
            $products = $this->getDiscountedProducts(5);
            return $this->formatProductResponse($products);
        }

        // Hỏi về hướng dẫn chọn size
        if (str_contains($message, 'size') || str_contains($message, 'kích thước') || str_contains($message, 'cỡ')) {
            return [
                'type' => 'text_with_image',
                'content' => "Dưới đây là bảng size áo tham khảo của TST Fashion. Bạn có thể cho mình biết chiều cao/cân nặng để tư vấn size phù hợp nhé!",
                'image_url' => 'https://res.cloudinary.com/dc2zvj1u4/image/upload/v1748404290/ao/file_u0eqqq.jpg'
            ];
        }
        // Từ ngữ không phù hợp
        if (preg_match('/\b(xấu|dở|tệ|chán|đểu|ngu)\b/u', $message)) {
            return ['type' => 'text', 'content' => "Xin lỗi nếu sản phẩm chưa làm bạn hài lòng. Mình có thể giúp gì để cải thiện trải nghiệm mua sắm của bạn không ạ?"];
        }

        $productKeywords = $this->detectProductKeywords($message);
        if (!empty($productKeywords)) {

            // XỬ LÝ PRICE INTENT
            if (isset($productKeywords['intent'])) {
                $products = $this->getProductRecommendations(
                    $productKeywords['category'] ?? null,
                    $productKeywords['keywords'] ?? [],
                    5, // limit
                    0, // offset
                    $productKeywords['intent'], // intent mới
                    $productKeywords['min_price'] ?? null,
                    $productKeywords['max_price'] ?? null
                );

                if (!empty($products)) {
                    $this->saveProductsToContext($userId, $products, $message);

                    // CUSTOM RESPONSE THEO INTENT
                    return $this->formatPriceIntentResponse($products, $productKeywords);
                } else {
                    return [
                        'type' => 'text',
                        'content' => "Xin lỗi, không tìm thấy sản phẩm phù hợp với yêu cầu '{$productKeywords['matched_term']}' của bạn."
                    ];
                }
            }

            // XỬ LÝ THÔNG THƯỜNG (không có intent về giá)
            $products = $this->getProductRecommendations($productKeywords['category'], $productKeywords['keywords']);
            if (!empty($products)) {
                $this->saveProductsToContext($userId, $products, $message);
                return $this->formatProductResponse($products);
            } else {
                return ['type' => 'text', 'content' => "Xin lỗi, hiện tại mình chưa tìm thấy sản phẩm '{$productKeywords['matched_term']}' phù hợp."];
            }
        }

        return null;
    }


    protected function formatPriceIntentResponse(array $products, array $intent): array
    {
        switch ($intent['intent']) {
            case 'cheapest':
                $product = $products[0];
                return [
                    'type' => 'text',
                    'content' => "Sản phẩm {$intent['matched_term']} là: **{$product['name']}** - {$product['price']}. Bạn có muốn xem chi tiết không?"
                ];

            case 'most_expensive':
                $product = $products[0];
                return [
                    'type' => 'text',
                    'content' => "Sản phẩm {$intent['matched_term']} là: **{$product['name']}** - {$product['price']}. Bạn có muốn xem chi tiết không?"
                ];

            case 'under_price':
            case 'price_range':
                $productData = [];
                foreach ($products as $product) {
                    $productData[] = [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'original_price' => $product['original_price'] ?? null,
                        'discount_percent' => $product['discount_percent'] ?? null,
                        'link' => $product['link'],
                        'image_url' => $product['image'],
                        'has_discount' => $product['has_discount'] ?? false
                    ];
                }

                return [
                    'type' => 'product_list',
                    'intro_message' => "Dưới đây là các sản phẩm {$intent['matched_term']}:",
                    'products' => $productData,
                    'outro_message' => "Bạn muốn xem chi tiết sản phẩm nào ạ?"
                ];

            default:
                return $this->formatProductResponse($products);
        }
    }






    protected function detectProductKeywords(string $message): array
    {
        $message = mb_strtolower(trim($message));

        // DETECT PRICE INTENT TRƯỚC
        $priceIntent = $this->detectPriceIntent($message);
        if ($priceIntent) {
            return $priceIntent;
        }

        // Định nghĩa từ khóa sản phẩm chi tiết (giữ nguyên)
        $productMap = [
            'áo sơ mi' => ['category' => 'áo sơ mi', 'keywords' => ['sơ mi', 'shirt']],
            'sơ mi' => ['category' => 'áo sơ mi', 'keywords' => ['sơ mi', 'shirt']],
            'áo thun' => ['category' => 'áo thun', 'keywords' => ['thun', 't-shirt', 'tshirt']],
            'thun' => ['category' => 'áo thun', 'keywords' => ['thun', 't-shirt']],
            'quần' => ['category' => 'quần', 'keywords' => ['pants', 'trousers']],
            'áo' => ['category' => 'áo', 'keywords' => ['shirt', 'top']],
        ];

        // Tìm kiếm exact match
        $sortedKeys = array_keys($productMap);
        usort($sortedKeys, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($sortedKeys as $keyword) {
            if (str_contains($message, $keyword)) {
                return [
                    'category' => $productMap[$keyword]['category'],
                    'keywords' => $productMap[$keyword]['keywords'],
                    'matched_term' => $keyword
                ];
            }
        }

        return [];
    }

    // HÀM MỚI: Phát hiện intent về giá
    protected function detectPriceIntent(string $message): ?array
    {
        // Detect category trước
        $category = null;
        if (str_contains($message, 'áo thun')) $category = 'áo thun';
        elseif (str_contains($message, 'áo sơ mi') || str_contains($message, 'sơ mi')) $category = 'áo sơ mi';
        elseif (str_contains($message, 'quần')) $category = 'quần';
        elseif (str_contains($message, 'áo')) $category = 'áo';

        // 1. RẺ NHẤT
        if (str_contains($message, 'rẻ nhất') || str_contains($message, 'giá rẻ nhất')) {
            return [
                'intent' => 'cheapest',
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => $category ? "$category rẻ nhất" : "sản phẩm rẻ nhất"
            ];
        }

        // 2. ĐẮT NHẤT
        if (str_contains($message, 'đắt nhất') || str_contains($message, 'giá cao nhất')) {
            return [
                'intent' => 'most_expensive',
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => $category ? "$category đắt nhất" : "sản phẩm đắt nhất"
            ];
        }

        // 3. DƯỚI GIÁ X
        if (preg_match('/dưới\s*(\d+[\d\.,]*)[k]?/u', $message, $matches)) {
            $price = (int)str_replace(['.', ','], '', $matches[1]);
            if (str_contains($matches[1], 'k') || $price < 10000) {
                $price *= 1000; // Convert 500k -> 500000
            }

            return [
                'intent' => 'under_price',
                'max_price' => $price,
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => "dưới " . number_format($price) . "đ"
            ];
        }

        // 4. KHOẢNG GIÁ
        if (preg_match('/từ\s*(\d+[\d\.,]*)[k]?\s*[-đến]*\s*(\d+[\d\.,]*)[k]?/u', $message, $matches)) {
            $minPrice = (int)str_replace(['.', ','], '', $matches[1]);
            $maxPrice = (int)str_replace(['.', ','], '', $matches[2]);

            if ($minPrice < 10000) $minPrice *= 1000;
            if ($maxPrice < 10000) $maxPrice *= 1000;

            return [
                'intent' => 'price_range',
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => number_format($minPrice) . "đ - " . number_format($maxPrice) . "đ"
            ];
        }

        return null;
    }

    // Lưu context của sản phẩm vào redis
    protected function saveProductsToContext(string $userId, array $products, string $userQuery): void
    {
        $productContextKey = "product_context:$userId";

        foreach ($products as $product) {
            $contextItem = [
                'name' => $product['name'],
                'price' => $product['price'],
                'original_price' => $product['original_price'] ?? null,
                'discount_percent' => $product['discount_percent'] ?? null,
                'link' => $product['link'],
                'image' => $product['image_url'] ?? $product['image'],
                'query' => $userQuery,
                'timestamp' => time(),
                'details' => $this->extractProductDetails($product)
            ];

            if (!$this->isProductInContext($userId, $product['name'])) {
                Redis::rpush($productContextKey, json_encode($contextItem));
            }
        }

        Redis::ltrim($productContextKey, -20, -1);
        Redis::expire($productContextKey, 60 * 60 * 2);
    }

    //Kiểm tra sản phẩm đã có trong context chưa
    protected function isProductInContext(string $userId, string $productName): bool
    {
        $productContextKey = "product_context:$userId";
        $contextRaw = Redis::lrange($productContextKey, 0, -1);

        foreach ($contextRaw as $itemRaw) {
            $item = json_decode($itemRaw);
            if ($item && $item->name === $productName) {
                return true;
            }
        }

        return false;
    }

    // Trích xuất chi tiết sản phẩm
    protected function extractProductDetails(array $product): string
    {
        // Có thể mở rộng để lấy thêm thông tin từ database
        $details = [];

        // Phân tích từ tên sản phẩm
        $name = mb_strtolower($product['name']);

        if (str_contains($name, 'cotton')) {
            $details[] = 'chất liệu cotton';
        }
        if (str_contains($name, 'basic')) {
            $details[] = 'thiết kế basic';
        }
        if (str_contains($name, 'polo')) {
            $details[] = 'dáng polo';
        }

        return implode(', ', $details);
    }

    // Tóm tắt lịch sử chat
    protected function summarizeHistory(array $historyMessages): string
    {
        $textToSummarize = "";
        foreach ($historyMessages as $msg) {
            $role = strtoupper($msg->role);
            $text = $msg->message;
            $textToSummarize .= "$role: $text\n";
        }

        $summaryPrompt = "Hãy tóm tắt ngắn gọn cuộc hội thoại mua sắm thời trang này thành 3-4 câu, tập trung vào:
                        - Sản phẩm khách quan tâm
                        - Vấn đề khách gặp phải
                        - Giải pháp đã đề xuất
                        - Trạng thái đơn hàng (nếu có)
                        Nội dung:\n" . $textToSummarize;

        $payload = [
            'model' => 'gemma3:4b',
            'prompt' => $summaryPrompt,
            'stream' => false,
        ];

        $response = Http::timeout(60)->post('http://localhost:11434/api/generate', $payload);

        if (!$response->successful()) {
            return '';
        }

        $data = $response->json();
        return trim($data['response'] ?? '');
    }


    // Hàm xử lý sản phẩm
    protected function getProductRecommendations(
        ?string $category = null,
        array $keywords = [],
        int $limit = 5,
        int $offset = 0,
        ?string $intent = null,
        ?int $minPrice = null,
        ?int $maxPrice = null
    ): array {
        $now = now();

        $query = Product::with(['category', 'discount' => function ($query) use ($now) {
            $query->where('status', 'active')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now);
        }])->select('id', 'product_name', 'price', 'slug', 'image', 'discount_id');

        // FILTER THEO CATEGORY/KEYWORDS
        if ($category) {
            $query->where(function ($q) use ($category, $keywords) {
                $q->whereHas('category', function ($subQ) use ($category) {
                    $subQ->where('category_name', 'like', '%' . $category . '%');
                });

                if (!empty($keywords)) {
                    $q->orWhere(function ($subQ) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $subQ->orWhere('product_name', 'like', '%' . $keyword . '%');
                        }
                    });
                }
            });
        } elseif (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('product_name', 'like', '%' . $keyword . '%');
                }
            });
        }

        // FILTER THEO GIÁ
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        // ORDER BY THEO INTENT
        switch ($intent) {
            case 'cheapest':
                $query->orderBy('price', 'asc');
                $limit = 3;
                break;

            case 'most_expensive':
                $query->orderBy('price', 'desc');
                $limit = 3;
                break;

            case 'under_price':
            case 'price_range':
                $query->orderBy('price', 'asc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
        }

        $dbProducts = $query->offset($offset)->limit($limit)->get();

        $formattedProducts = [];
        foreach ($dbProducts as $product) {
            $price = $product->price;
            $originalPrice = null;
            $discountPercent = null;
            $discountCode = null;

            if (
                $product->relationLoaded('discount') &&
                $product->discount &&
                $product->discount->status === 'active' &&
                now()->between($product->discount->start_date, $product->discount->end_date)
            ) {
                $originalPrice = $price;
                $discountPercent = $product->discount->percent_discount; // Giả sử đây là float (0.1 = 10%)
                $discountCode = $product->discount->code;
                $price = $price * (1 - $discountPercent); // Nhân với % khuyến mãi (float)
            }

            $formattedProducts[] = [
                'name' => $product->product_name,
                'price' => $this->formatPrice($price),
                'original_price' => $originalPrice ? $this->formatPrice($originalPrice) : null,
                'discount_percent' => $discountPercent,
                'discount_code' => $discountCode,
                'discount_name' => $product->discount->name ?? null,
                'link' => '/product/' . $product->slug,
                'image' => $product->image,
                'has_discount' => !is_null($discountPercent)
            ];
        }

        return $formattedProducts;
    }


    protected function getDiscountedProducts(int $limit = 5): array
    {
        $now = now();

        $products = Product::with(['category', 'discount' => function ($query) use ($now) {
            $query->where('status', 'active')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now);
        }])
            ->whereHas('discount', function ($query) use ($now) {
                $query->where('status', 'active')
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Xử lý định dạng trực tiếp ở đây
        $formattedProducts = [];
        foreach ($products as $product) {
            $price = $product->price;
            $originalPrice = $price;
            $discountPercent = $product->discount->percent_discount;
            $price = $price * (1 - $discountPercent); // Nhân với % khuyến mãi (float)

            $formattedProducts[] = [
                'name' => $product->product_name,
                'price' => $this->formatPrice($price),
                'original_price' => $this->formatPrice($originalPrice),
                'discount_percent' => $discountPercent,
                'discount_code' => $product->discount->code,
                'discount_name' => $product->discount->name,
                'link' => '/product/' . $product->slug,
                'image' => $product->image,
                'has_discount' => true
            ];
        }

        return $formattedProducts;
    }

    // Định dạng tiền
    protected function formatPrice($price)
    {
        return number_format($price, 0, ',', '.') . 'đ';
    }


    protected function formatProductResponse(array $products, bool $isMoreRequest = false): array
    {
        $productData = [];
        foreach ($products as $product) {
            $discountPercentDisplay = $product['discount_percent'] ? round($product['discount_percent'] * 100) : 0;

            $productData[] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'original_price' => $product['original_price'] ?? null,
                'discount_percent' => $discountPercentDisplay,
                'discount_code' => $product['discount_code'] ?? null,
                'discount_name' => $product['discount_name'] ?? null,
                'link' => $product['link'],
                'image_url' => $product['image'],
                'has_discount' => $product['has_discount'] ?? false
            ];
        }

        return [
            'type' => 'product_list',
            'intro_message' => $isMoreRequest
                ? "Dưới đây là thêm một số sản phẩm tương tự dành cho bạn:"
                : "Mình xin gợi ý một số sản phẩm dành cho bạn:",
            'products' => $productData,
            'outro_message' => "\nBạn muốn xem chi tiết sản phẩm nào ạ?",
        ];
    }


    protected function processContentWithImages(string $content): string
    {
        // Tìm và thay thế các URL ảnh thành img tags
        $imagePattern = '/(https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp))/i';

        $processedContent = preg_replace_callback($imagePattern, function ($matches) {
            $imageUrl = $matches[0];
            return "<img src='{$imageUrl}' alt='Hình ảnh' style='max-width: 100%; height: auto; border-radius: 8px; margin: 10px 0;'>";
        }, $content);

        return $processedContent;
    }
}
