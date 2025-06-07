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
