<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ChatBotApiController extends Controller
{


// **************** Luồng xử lý chính trong ChatBotApiController có thể tóm tắt ngắn gọn như sau **************
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
    // CÁCH CŨ DÙNG CONTEXT
    // public function sendMessage(Request $request)
    // {
    //     // $userId = 'user_123'; // tuỳ hệ thống, bạn có thể lấy từ Auth::id()
    //     $userId = 'user_gemma3';
    //     // $userId = 'user_gemma3_12b';

    //     $prompt = $request->input('message');

    //     // Lấy context từ Redis nếu có
    //     $contextJson = Redis::get("chat_context:$userId");
    //     $context = $contextJson ? json_decode($contextJson) : null;

    //     // Gửi tới OLLama
    //     $payload = [
    //         // 'model' => 'llama3.2:latest',
    //         'model' => 'gemma3:4b',
    //         // 'model' => 'gemma3:12b',
    //         'prompt' => $prompt,
    //         'stream' => false
    //     ];

    //     if ($context) {
    //         $payload['context'] = $context;
    //     }

    //     $response = Http::post('http://localhost:11434/api/generate', $payload);

    //     if (!$response->successful()) {
    //         return response()->json(['error' => 'Failed to connect to OLLama.'], 500);
    //     }

    //     $data = $response->json();

    //     // Lưu context mới nếu có
    //     if (!empty($data['context'])) {
    //         Redis::set("chat_context:$userId", json_encode($data['context']));
    //     }

    //     // Giả sử bạn muốn trả về ảnh từ URL trong response nếu có
    //     $imageUrl = null;
    //     if (preg_match('/image-url-pattern/', $data['response'])) {  // Nếu có URL ảnh trong phản hồi
    //         $imageUrl = 'https://example.com/path/to/image.jpg';  // Cập nhật URL ảnh thực tế từ phản hồi
    //     }

    //     return response()->json([
    //         'reply' => $data['response'] ?? '[Không có phản hồi từ AI]',
    //         'imageUrl' => $imageUrl // Trả về URL ảnh nếu có
    //     ]);
    // }

    // public function chatbot()
    // {
    //     return view('sites.chatbotRedis.chatbot');
    // }

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
        - Kèm link sản phẩm khi có thể

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
        ";


    // Hàm xử lý gửi tin nhắn
    public function sendMessage(Request $request)
    {
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

        // Lưu tin nhắn vào history
        Redis::rpush($historyKey, json_encode(['role' => 'user', 'message' => $userMessage]));
        Redis::rpush($historyKey, json_encode(['role' => 'assistant', 'message' => $reply]));
        Redis::ltrim($historyKey, -$maxMessages, -1);
        Redis::expire($historyKey, 60 * 60 * 24);

        return response()->json(['reply' => $reply]);
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

        // Hỏi về khuyến mãi
        if (str_contains($message, 'khuyến mãi') || str_contains($message, 'giảm giá') || str_contains($message, 'sale')) {
            return ['type' => 'text', 'content' => "Hiện đang có chương trình cho một số sản phẩm . Bạn có thể xem chi tiết tại <a href='http://127.0.0.1:8000/shop'>đây</a>."];
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

        // XỬ LÝ SẢN PHẨM - ĐƯỢC CẬP NHẬT VỚI KEYWORD MATCHING THÔNG MINH
        $productKeywords = $this->detectProductKeywords($message);
        if (!empty($productKeywords)) {
            $products = $this->getProductRecommendations($productKeywords['category'], $productKeywords['keywords']);
            if (!empty($products)) {
                // LƯU SẢN PHẨM VÀO REDIS CONTEXT
                $this->saveProductsToContext($userId, $products, $message);

                return $this->formatProductResponse($products);
            } else {
                return ['type' => 'text', 'content' => "Xin lỗi, hiện tại mình chưa tìm thấy sản phẩm '{$productKeywords['matched_term']}' phù hợp. Bạn có muốn xem các sản phẩm tương tự không?"];
            }
        }
        return null;
    }

    // Phát hiện từ khóa sản phẩm thông minh
    protected function detectProductKeywords(string $message): array
    {
        $message = mb_strtolower(trim($message));

        // Định nghĩa từ khóa sản phẩm chi tiết
        $productMap = [
            // Áo sơ mi
            'áo sơ mi' => ['category' => 'áo sơ mi', 'keywords' => ['sơ mi', 'shirt']],
            'sơ mi' => ['category' => 'áo sơ mi', 'keywords' => ['sơ mi', 'shirt']],
            'áo sơ mi linen' => ['category' => 'áo sơ mi', 'keywords' => ['sơ mi', 'linen']],
            'áo sơ mi cotton' => ['category' => 'áo sơ mi', 'keywords' => ['sơ mi', 'cotton']],

            // Áo thun
            'áo thun' => ['category' => 'áo thun', 'keywords' => ['thun', 't-shirt', 'tshirt']],
            'áo tshirt' => ['category' => 'áo thun', 'keywords' => ['thun', 't-shirt', 'tshirt']],
            'áo t-shirt' => ['category' => 'áo thun', 'keywords' => ['thun', 't-shirt', 'tshirt']],
            'thun' => ['category' => 'áo thun', 'keywords' => ['thun', 't-shirt']],

            // Áo polo
            'áo polo' => ['category' => 'áo polo', 'keywords' => ['polo']],
            'polo' => ['category' => 'áo polo', 'keywords' => ['polo']],

            // Áo khoác
            'áo khoác' => ['category' => 'áo khoác', 'keywords' => ['khoác', 'jacket']],
            'jacket' => ['category' => 'áo khoác', 'keywords' => ['khoác', 'jacket']],

            // Quần
            'quần jean' => ['category' => 'quần', 'keywords' => ['jean', 'jeans']],
            'quần jeans' => ['category' => 'quần', 'keywords' => ['jean', 'jeans']],
            'quần tây' => ['category' => 'quần', 'keywords' => ['tây', 'trousers']],
            'quần short' => ['category' => 'quần', 'keywords' => ['short', 'shorts']],
            'quần' => ['category' => 'quần', 'keywords' => ['pants', 'trousers']],

            // Tổng quát
            'áo' => ['category' => 'áo', 'keywords' => ['shirt', 'top']],
        ];

        // Tìm kiếm exact match trước (ưu tiên từ khóa dài hơn)
        $sortedKeys = array_keys($productMap);
        usort($sortedKeys, function ($a, $b) {
            return strlen($b) - strlen($a); // Sắp xếp từ dài đến ngắn
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

        // Kiểm tra từ khóa chung
        if (str_contains($message, 'sản phẩm') || str_contains($message, 'hàng')) {
            return [
                'category' => null,
                'keywords' => [],
                'matched_term' => 'sản phẩm'
            ];
        }

        return [];
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
    protected function getProductRecommendations(?string $category = null, array $keywords = [],  int $limit = 5,  int $offset = 0): array
    {
        $now = now(); // Lấy thời gian hiện tại để kiểm tra khuyến mãi

        $query = Product::with(['category', 'discount' => function ($query) use ($now) {
            $query->where('status', 'active')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now);
        }])->select('id', 'product_name', 'price', 'slug', 'image', 'discount_id');
        // Nếu có category cụ thể
        if ($category) {
            $query->where(function ($q) use ($category, $keywords) {
                // Kiểm tra theo category name
                $q->whereHas('category', function ($subQ) use ($category) {
                    $subQ->where('category_name', 'like', '%' . $category . '%');
                });

                // Hoặc kiểm tra theo product name với keywords
                if (!empty($keywords)) {
                    $q->orWhere(function ($subQ) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $subQ->orWhere('product_name', 'like', '%' . $keyword . '%');
                        }
                    });
                }
            });
        }
        // Nếu chỉ có keywords mà không có category
        elseif (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('product_name', 'like', '%' . $keyword . '%');
                }
            });
        }

        // $dbProducts = $query->limit(5)->get();
        $dbProducts = $query->offset($offset)->limit($limit)->get();
        $formattedProducts = [];
        foreach ($dbProducts as $product) {
            $price = $product->price;
            $originalPrice = null;
            $discountPercent = null;
            $discountCode = null;

            // Kiểm tra khuyến mãi hợp lệ
            if (
                $product->relationLoaded('discount') &&
                $product->discount &&
                $product->discount->status === 'active' &&
                $now->between($product->discount->start_date, $product->discount->end_date)
            ) {

                $originalPrice = $price;
                $discountPercent = $product->discount->percent_discount;
                $discountCode = $product->discount->code;
                $price = $price * (1 - $discountPercent);
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

    // Định dạng tiền
    protected function formatPrice($price)
    {
        return number_format($price, 0, ',', '.') . 'đ';
    }


    // Định dạng sản phẩm trên giao diện
    protected function formatProductResponse(array $products, bool $isMoreRequest = false): array
    {
        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'original_price' => $product['original_price'] ?? null,
                'discount_percent' => $product['discount_percent'] ?? null,
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
}
