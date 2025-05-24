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

    public function chatbot()
    {
        return view('sites.chatbotRedis.chatbot');
    }

    protected $defaultPrompt = "
                Bạn là một trợ lý chatbot thông minh cho TST Fashion Shop - cửa hàng thời trang online tại Việt Nam. Hãy luôn thân thiện, chuyên nghiệp và hữu ích.
                ### THÔNG TIN CỬA HÀNG:
                - Địa chỉ chi nhánh Cần Thơ: 3/2, Xuân Khánh, Cần Thơ
                - Chính sách đổi trả: 30 ngày
                - Phương thức thanh toán: COD, VNPay, Momo, ZaloPay
                - Size áo/quần: XS, S, M, L, XL, XXL

                ### HƯỚNG DẪN PHẢN HỒI:
                1. Khi hỏi về sản phẩm:
                - Kiểm tra database trước
                - Nếu không tìm thấy, đề xuất sản phẩm tương tự (áo/quần/phụ kiện)
                - Cung cấp thông tin chi tiết: chất liệu, size, màu sắc, giá
                - Kèm link sản phẩm khi có thể

                2. Khi hỏi về chính sách:
                - Đổi trả: 30 ngày, điều kiện sản phẩm nguyên tag
                - Thanh toán: COD hoặc ví điện tử
                - Vận chuyển: Miễn phí đơn >500k

                3. Hỗ trợ mua hàng:
                - Hướng dẫn thêm vào giỏ hàng
                - Hỗ trợ thanh toán
                - Theo dõi đơn hàng (cung cấp form mẫu)

                4. Tư vấn thời trang:
                - Gợi ý phối đồ theo mùa/dịp
                - Tư vấn size phù hợp với chiều cao/cân nặng
                - Xu hướng thời trang hiện tại

                5. Xử lý phản hồi:
                - Khen ngợi: Cảm ơn và tương tác tích cực
                - Phàn nàn: Xin lỗi và đề xuất giải pháp
                - Từ ngữ không phù hợp: Nhắc nhở nhẹ nhàng

                ### LIÊN KẾT QUAN TRỌNG:
                - Trang liên hệ: <a href='http://127.0.0.1:8000/contact'>Contacts</a>
                - Blog thời trang: <a href='http://127.0.0.1:8000/blog'>Blog</a>
                - Cửa hàng: <a href='http://127.0.0.1:8000/shop'>Shop</a>
                - Hướng dẫn chọn size: <a href='http://127.0.0.1:8000/size-guide'>Size Guide</a>

                ### LƯU Ý:
                - Luôn giữ thái độ tích cực
                - Không tiết lộ thông tin cá nhân khách hàng
                - Chuyển sang nhân viên khi không xử lý được
                ";





    // Hàm tóm tắt lịch sử chat
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
        $summary = $data['response'] ?? '';

        return trim($summary);
    }



    // Hàm xử lý gửi tin nhắn (*******)
    public function sendMessage(Request $request)
    {
        $userId = 'user_gemma3_newway'; // Có thể thay bằng Auth::id()
        $userMessage = $request->input('message');
        $historyKey = "chat_history:$userId";
        $maxMessages = 50;
        $summarizeThreshold = 50; // Khi số tin nhắn vượt ngưỡng thì tóm tắt


        $specialResponse = $this->handleSpecialCases($userMessage);
        if ($specialResponse) {
            return response()->json([
                'reply_data' => $specialResponse,
                'reply' => $specialResponse['content'] ?? $specialResponse['message'] ?? '' // Thêm fallback
            ]);
        }

        // Bước 1: Lấy toàn bộ lịch sử chat hiện tại trong Redis
        $historyRaw = Redis::lrange($historyKey, 0, -1);
        $history = array_map('json_decode', $historyRaw);

        // Bước 2: Nếu số lượng tin nhắn vượt quá ngưỡng, tóm tắt lịch sử cũ
        if (count($history) >= $summarizeThreshold) {
            // Lấy 40 tin nhắn đầu tiên để tóm tắt
            $toSummarize = array_slice($history, 0, 40);
            $summaryText = $this->summarizeHistory($toSummarize);

            if ($summaryText) {
                // Xóa phần đã tóm tắt khỏi lịch sử, giữ lại 10 tin nhắn mới nhất + 1 tin nhắn tóm tắt dạng system
                $history = array_slice($history, 40);
                array_unshift($history, (object)[
                    'role' => 'system',
                    'message' => $summaryText
                ]);

                // Xóa toàn bộ key cũ và lưu lại lịch sử mới đã tóm tắt vào Redis
                Redis::del($historyKey);
                foreach ($history as $item) {
                    Redis::rpush($historyKey, json_encode($item));
                }
            }
        }

        // Bước 3: Lấy lại 20 tin nhắn cuối để tạo prompt multi-turn
        $recentRaw = Redis::lrange($historyKey, -20, -1);
        $recentHistory = array_map('json_decode', $recentRaw);

        // Prompt mặc định cố định, không đổi trong suốt phiên
        $defaultSystemPrompt = $this->defaultPrompt;
        // $defaultSystemPrompt = "Bạn là chatbot hỗ trợ khách hàng TST Fashion Shop, vui vẻ và thân thiện.";

        // Tách riêng phần tóm tắt (system) nếu có trong lịch sử chat
        $systemMsg = '';
        $chatPrompt = '';
        foreach ($recentHistory as $item) {
            if ($item->role === 'system') {
                if (!empty($systemMsg)) {
                    $systemMsg .= "\n" . $item->message;
                } else {
                    $systemMsg = $item->message;
                }
                continue;
            }

            if (in_array($item->role, ['user', 'assistant'])) {
                $role = strtoupper($item->role);
                $message = $item->message;
                $chatPrompt .= "$role: $message\n";
            }
        }

        // Kết hợp prompt mặc định + tóm tắt lịch sử chat (nếu có)
        $fullSystemPrompt = $defaultSystemPrompt;
        if (!empty($systemMsg)) {
            $fullSystemPrompt .= "\nTóm tắt lịch sử chat trước đây: " . $systemMsg;
        }

        // Xây dựng prompt gửi cho AI
        $chatPrompt = "SYSTEM: $fullSystemPrompt\n" . $chatPrompt;
        $chatPrompt .= "USER: $userMessage\nASSISTANT:";

        // Bước 4: Gửi prompt tới Ollama
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

        // Xử lý loại bỏ chữ "ASSISTANT:" nếu AI trả lời có phần này
        $replyRaw = $data['response'] ?? '[Không có phản hồi từ AI]';
        $reply = preg_replace('/^ASSISTANT:\s*/i', '', $replyRaw);

        // Bước 5: Lưu tin nhắn user và AI vào Redis
        Redis::rpush($historyKey, json_encode(['role' => 'user', 'message' => $userMessage]));
        Redis::rpush($historyKey, json_encode(['role' => 'assistant', 'message' => $reply]));

        // Giữ lại tối đa $maxMessages tin nhắn
        Redis::ltrim($historyKey, -$maxMessages, -1);

        // Có thể đặt TTL key để tự động xoá sau 1 ngày (tuỳ nhu cầu)
        Redis::expire($historyKey, 60 * 60 * 24);

        return response()->json([
            'reply' => $reply
        ]);
    }

    // Hàm xử lý trường hợp đặc biệt
    protected function handleSpecialCases(string $message): ?array
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

        if (str_contains($message, 'sản phẩm') || str_contains($message, 'áo') || str_contains($message, 'quần')) {
            $category = null;
            if (str_contains($message, 'áo')) {
                $category = 'áo';
            } elseif (str_contains($message, 'quần')) {
                $category = 'quần';
            }
            // Có thể thêm logic phức tạp hơn để phân tích category từ tin nhắn

            $products = $this->getProductRecommendations($category);
            if (!empty($products)) {
                return $this->formatProductResponse($products); // Hàm này đã trả về array
            } else {
                return ['type' => 'text', 'content' => "Xin lỗi, hiện tại mình chưa tìm thấy sản phẩm phù hợp với yêu cầu của bạn. Bạn có muốn xem tất cả sản phẩm không?"];
            }
        }
        return null; // Trả về null nếu không có trường hợp đặc biệt nào
    }


    // Xử lý sản phẩm
    protected function getProductRecommendations(?string $category = null): array
    {
        $query = Product::with('category')->select('product_name', 'price', 'slug', 'image'); // Chọn các trường bạn muốn lấy

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category_name', 'like', '%' . $category . '%');
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
        // Thay vì trả về một chuỗi, bây giờ chúng ta sẽ trả về một mảng chứa thông tin sản phẩm
        // để frontend có thể render tùy chỉnh.
        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'link' => $product['link'],
                'image_url' => $product['image'], // Thêm URL ảnh
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

































// DEMO





















