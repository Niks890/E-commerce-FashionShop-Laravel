<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ChatBotApiController extends Controller
{

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

    //  **************Flow xử lý tin nhắn***************
    //  User Message -> Special Cases Check  -> [Nếu không match] → Lấy lịch sử từ Redis
    //      -> Check threshold → [Nếu > 50] → Tóm tắt -> Xây dựng Multi-turn Prompt
    //        -> call api tới Ollama AI -> Xử lý Response -> Lưu vào Redis(list string) ->Return JSON Response

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
        - Hướng dẫn chọn size: <a href='http://127.0.0.1:8000/size-guide'>Size Guide</a>
        ";

    public function sendMessage(Request $request)
    {
        $userId = 'user_gemma3_newway';
        $userMessage = $request->input('message');
        $historyKey = "chat_history:$userId";
        $productContextKey = "product_context:$userId"; // Key mới cho product context
        $maxMessages = 50;
        $summarizeThreshold = 50;

        // Kiểm tra special cases trước
        $specialResponse = $this->handleSpecialCases($userMessage, $userId);
        if ($specialResponse) {
            return response()->json([
                'reply_data' => $specialResponse,
                'reply' => $specialResponse['content'] ?? $specialResponse['message'] ?? ''
            ]);
        }

        // Lấy và xử lý lịch sử chat
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

    // Hàm xử lý trường hợp đặc biệt - ĐƯỢC CẬP NHẬT
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
            return ['type' => 'text', 'content' => "Hiện đang có chương trình giảm 20% cho áo thun và 15% cho quần jeans. Bạn có thể xem chi tiết tại <a href='http://127.0.0.1:8000/promotions'>đây</a>."];
        }

        // Hỏi về hướng dẫn chọn size
        if (str_contains($message, 'chọn size') || str_contains($message, 'hướng dẫn size')) {
            return ['type' => 'text', 'content' => "Bạn có thể tham khảo hướng dẫn chọn size tại <a href='http://127.0.0.1:8000/size-guide'>đây</a>. Hoặc cho mình biết chiều cao/cân nặng để tư vấn cụ thể nhé!"];
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

    // HÀM MỚI: Phát hiện từ khóa sản phẩm thông minh
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
    protected function saveProductsToContext(string $userId, array $products, string $userQuery): void
    {
        $productContextKey = "product_context:$userId";

        foreach ($products as $product) {
            $contextItem = [
                'name' => $product['name'],
                'price' => $product['price'],
                'link' => $product['link'],
                'image' => $product['image'],
                'query' => $userQuery, // Lưu câu hỏi gốc
                'timestamp' => time(),
                'details' => $this->extractProductDetails($product) // Thêm chi tiết nếu có
            ];

            // Kiểm tra xem sản phẩm đã tồn tại chưa
            if (!$this->isProductInContext($userId, $product['name'])) {
                Redis::rpush($productContextKey, json_encode($contextItem));
            }
        }

        // Giới hạn số lượng sản phẩm trong context (tối đa 20)
        Redis::ltrim($productContextKey, -20, -1);

        // Set TTL cho product context (2 giờ)
        Redis::expire($productContextKey, 60 * 60 * 2);
    }

    // HÀM MỚI: Kiểm tra sản phẩm đã có trong context chưa
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

    // HÀM MỚI: Trích xuất chi tiết sản phẩm
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

    // Các hàm khác giữ nguyên...
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

    protected function getProductRecommendations(?string $category = null, array $keywords = []): array
    {
        $query = Product::with('category')->select('product_name', 'price', 'slug', 'image');

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

        $dbProducts = $query->limit(5)->get();

        $formattedProducts = [];
        foreach ($dbProducts as $product) {
            $formattedProducts[] = [
                'name' => $product->product_name,
                'price' => number_format($product->price, 0, ',', '.') . 'đ',
                'link' => '/product/' . $product->slug,
                'image' => $product->image
            ];
        }

        return $formattedProducts;
    }

    protected function formatProductResponse(array $products): array
    {
        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'link' => $product['link'],
                'image_url' => $product['image'],
            ];
        }

        return [
            'type' => 'product_list',
            'intro_message' => "Mình xin gợi ý một số sản phẩm dành cho bạn:",
            'products' => $productData,
            'outro_message' => "\nBạn muốn xem chi tiết sản phẩm nào ạ?",
        ];
    }
}
