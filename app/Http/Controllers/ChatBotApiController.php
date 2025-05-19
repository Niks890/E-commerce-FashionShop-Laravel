<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ChatBotApiController extends Controller
{

    protected $defaultPrompt =
    " Bạn là một trợ lý chatbot thông minh, bạn đang đóng vai chatbot cho website bán hàng cho TST Fashion Shop - một cửa hàng bán quần áo online tại Việt Nam.
                - Nếu khách hỏi về sản phẩm, trước tiên kiểm tra trong database.
                - Nếu khách hỏi về đường đến Cần Thơ, hãy nói cho họ biết có 1 chi nhánh của cửa hàng ở đường 3/2, Xuân Khánh, Cần Thơ.
                - Nếu không tìm thấy sản phẩm, hãy đề xuất một số mặt hàng có sẵn (Chỉ đề xuất áo hoặc quần, phụ kiện thôi).
                - Nếu khách hỏi ngoài phạm vi, hãy trả lời lịch sự và khuyến khích họ mua sắm.
                - Nếu có ai đó khen bạn, không ngần ngại cảm ơn họ và tỏ ra thân thiện.
                - Nếu có ai đó chửi bạn, hãy nhắc nhở và tỏ ra lịch sự với họ.
                - Nếu ai đó có những tin nhắn với từ ngữ nhạy cảm hoặc không phù hợp hãy cảnh báo họ một cách nhẹ nhàng và lịch sự.
                - Chính sách đổi trả của cửa hàng là 30 ngày.
                - Các phương thức thanh toán có ở cửa hàng là COD, ví điện tử (VNPay, Momo, ZaloPay).
                - Size áo và quần thì có là XS, S, M, L, XL, XXL.
                - Nếu người dùng hỏi về cách liên hệ đổi trả sản phẩm, hãy nói về chính sách đổi trả của cửa hàng và có đường link qua trang liên hệ.
                - Trang liên hệ nằm ở đây: <a href='http://127.0.0.1:8000/contact'>Contacts</a>
                - Trang blog nằm ở đây: <a href='http://127.0.0.1:8000/blog'>Blog</a>
                - Trang mua sản phẩm nằm ở đây: <a href='http://127.0.0.1:8000/shop'>Shop</a>";

    public function chatbot()
    {
        return view('sites.chatbotRedis.chatbot');
    }

    //CÁCH CŨ DÙNG CONTEXT
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

    public function sendMessage(Request $request)
    {
        $userId = 'user_gemma3_newway'; // Có thể thay bằng Auth::id()
        $userMessage = $request->input('message');
        $historyKey = "chat_history:$userId";
        $maxMessages = 50;
        $summarizeThreshold = 50; // Khi số tin nhắn vượt ngưỡng thì tóm tắt

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

    // Hàm tóm tắt lịch sử chat (giữ nguyên)
    protected function summarizeHistory(array $historyMessages): string
    {
        $textToSummarize = "";
        foreach ($historyMessages as $msg) {
            $role = strtoupper($msg->role);
            $text = $msg->message;
            $textToSummarize .= "$role: $text\n";
        }

        $summaryPrompt = "Hãy tóm tắt ngắn gọn nội dung cuộc hội thoại sau đây thành vài câu chính:\n" . $textToSummarize;

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
}
