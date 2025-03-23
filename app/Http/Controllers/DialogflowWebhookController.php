<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DialogflowWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // Ghi log để kiểm tra request từ Dialogflow
            Log::info('📥 Received Webhook Request:', $request->all());

            // Lấy intent và parameters từ request
            $intent = $request->input('queryResult.intent.displayName');
            $parameters = $request->input('queryResult.parameters', []);

            if ($intent === "iProducts") {
                $productName = $parameters['product'] ?? '';

                // Kiểm tra nếu không có tên sản phẩm
                if (empty($productName)) {
                    return response()->json(["fulfillmentText" => "Bạn muốn tìm sản phẩm nào?"]);
                }

                // Truy vấn trực tiếp từ database
                $products = DB::table('products')
                    ->where('product_name', 'LIKE', "%{$productName}%")
                    ->select('product_name', 'price')
                    ->get();

                if ($products->isNotEmpty()) {
                    $responseText = "💡 Sản phẩm bạn tìm thấy:\n";
                    foreach ($products as $product) {
                        $formattedPrice = number_format($product->price, 0, ',', '.');
                        $responseText .= "- {$product->product_name} ({$formattedPrice} VND)\n";
                    }
                } else {
                    $responseText = "❌ Xin lỗi, tôi không tìm thấy sản phẩm phù hợp.";
                }

                return response()->json(["fulfillmentText" => $responseText]);
            }

            return response()->json(["fulfillmentText" => "❔ Xin lỗi, tôi không hiểu yêu cầu của bạn."]);
        } catch (\Exception $e) {
            // Ghi log lỗi
            Log::error("❌ Lỗi webhook Dialogflow: " . $e->getMessage());
            return response()->json(["fulfillmentText" => "Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!"]);
        }
    }
}
