<?php

namespace App\Http\Controllers;

use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;
use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DialogflowController extends Controller
{
    public function detectIntent(Request $request)
    {
        try {
            $projectId = 'chatbotlaravel';
            $text = $request->input('message');
            $sessionId = $request->input('session_id', session()->getId());
            $credentialsPath = storage_path('app/dialogflow/chatbotlaravel-f149074225c4.json');

            if (!file_exists($credentialsPath)) {
                return response()->json(['error' => 'File credentials không tồn tại']);
            }

            // Khởi tạo SessionsClient
            $sessionClient = new SessionsClient(['credentials' => $credentialsPath]);
            $session = $sessionClient->sessionName($projectId, $sessionId);

            // Tạo TextInput và QueryInput
            $textInput = new TextInput();
            $textInput->setText($text);
            $textInput->setLanguageCode('vi');

            $queryInput = new QueryInput();
            $queryInput->setText($textInput);

            // Tạo DetectIntentRequest
            $detectIntentRequest = new DetectIntentRequest();
            $detectIntentRequest->setSession($session);
            $detectIntentRequest->setQueryInput($queryInput);

            // Gửi yêu cầu và nhận phản hồi từ Dialogflow
            $response = $sessionClient->detectIntent($detectIntentRequest);
            $queryResult = $response->getQueryResult();
            $intent = $queryResult->getIntent()->getDisplayName();
            $replyMessage = $queryResult->getFulfillmentText();

            // Xử lý intent "iProducts"
            if ($intent === "iProducts") {
                $parameters = json_decode($queryResult->getParameters()->serializeToJsonString(), true);
                $productName = $parameters['product'] ?? '';

                if (!empty($productName)) {
                    $matchedProducts = DB::table('products')
                        ->where('product_name', 'LIKE', "%{$productName}%")
                        ->select('product_name', 'price')
                        ->get();

                    if ($matchedProducts->isNotEmpty()) {
                        $productList = $matchedProducts->map(function ($product) {
                            return "- {$product->product_name} (" . number_format($product->price, 0, ',', '.') . " VND)";
                        })->implode("\n");

                        $replyMessage = "Các sản phẩm bạn tìm thấy:\n" . $productList;
                    } else {
                        $replyMessage = "Xin lỗi, tôi không tìm thấy sản phẩm phù hợp.";
                    }
                } else {
                    $replyMessage = "Bạn muốn tìm sản phẩm nào? Vui lòng nói rõ hơn.";
                }
            }

            $sessionClient->close();

            return response()->json(['message' => $replyMessage]);
        } catch (\Exception $e) {
            Log::error("Lỗi trong quá trình xử lý: " . $e->getMessage());
            return response()->json(['error' => 'Đã xảy ra lỗi trong quá trình xử lý yêu cầu.']);
        }
    }
}
