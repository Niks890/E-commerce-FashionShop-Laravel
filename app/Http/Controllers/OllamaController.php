<?php

namespace App\Http\Controllers;
use App\AI\Ochat;
use Illuminate\Http\Request;

class OllamaController extends Controller
{
    public function ollamaRequest(Request $request) {
        $message = $request->input('message');
    
        // Gọi hàm gửi tin nhắn từ Ochat
        $chatbot = new Ochat();
        $response = $chatbot->send($message);
    
        // Đảm bảo response trả về dưới dạng JSON
        return response()->json(['response' => $response]);
    }
}
