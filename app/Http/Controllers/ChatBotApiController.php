<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ChatBotApiController extends Controller
{
    // Default system prompt
    protected $defaultPrompt = "
        Bạn là một trợ lý chatbot thông minh cho TFashion Shop - cửa hàng bán quần áo thời trang online tại Việt Nam.
        Hãy luôn thân thiện, chuyên nghiệp và hữu ích.
        ### QUY TẮC TUYỆT ĐỐI - KHÔNG ĐƯỢC VI PHẠM:
            1. **KHÔNG BAO GIỜ ĐƯỢC BỊA THÔNG TIN SẢN PHẨM**
            2. **CHỈ SỬ DỤNG THÔNG TIN CÓ SẴN TRONG product_context**
            3. **NẾU product_context RỖNG HOẶC KHÔNG CÓ DỮ LIỆU → KHÔNG ĐƯA THÔNG TIN SẢN PHẨM CỤ THỂ HÃY HỎI KHÁCH HÀNG CẦN GÌ**
        ### THÔNG TIN CỬA HÀNG:
        - Giờ mở cửa các chi nhánh là 8h - 22h hàng ngày
        - Địa chỉ chi nhánh Cần Thơ: 3/2, Xuân Khánh, Cần Thơ
        - Địa chỉ chi nhánh Hồ Chí Minh: Quận Cam, Hồ Chí Minh
        - Thời gian làm việc là toàn bộ các ngày trong tuần.
        - Fanpage Facebook: https://www.facebook.com/tfashionvn
        - Instagram: https://www.instagram.com/tfashionvn
        - Email Liên Hệ: vominhtri@gmail.com
        - Zalo liên hệ: 0123456789
        - Hotline: 0123456789
        - Chính sách đổi trả: Miễn phí vận chuyển, hỗ trợ đổi trả trong vòng 7 ngày
        - Điều kiện đổi trả: còn nguyên tem, chưa qua sử dụng, có hóa đơn.
        - Hoàn tiền: Liên hệ hotline để được tư vấn rõ.
        - Phí ship: Mua trên 500k free ship, dưới 500k phí 30k
        - Phương thức thanh toán: COD (được kiểm hàng trước khi thanh toán), VNPay, Momo, ZaloPay
        - Size áo/quần gồm: XS, S, M, L, XL, XXL.
        - Không hỗ trợ Gift Card,
        - Voucher (mã giảm giá) sẽ được tặng qua email cá nhân cho khách hàng thân thiết.
        ### CHÍNH SÁCH & DỊCH VỤ:
        - [Bảo hành] 1 đổi 1 trong 7 ngày nếu lỗi nhà sản xuất
        - [Giao hàng] Giao nhanh trong 2h tại nội thành Cần Thơ, có hỗ trợ ship toàn quốc
        - [Hỗ trợ] Tư vấn 24/7 qua hotline 0123456789
        ### HƯỚNG DẪN PHẢN HỒI:
            1. Khi khách hỏi về sản phẩm:
            - Khi khách hỏi về sản phẩm hoặc đặt hàng sản phẩm nào mà dữ liệu trong context
                và cuộc hội thoại là rỗng hoặc chưa có hãy hỏi rõ khách muốn mua gì,
                hoặc giới thiệu trang shop: <a href='http://127.0.0.1:8000/shop'>Shop</a> để tham khảo.
            - Sử dụng thông tin sản phẩm có sẵn trong context nếu có,
                tuyệt đối không được bịa ra mà hãy trả lời là không tìm thấy hoặc hiện chưa có sản phẩm đó.
            - Cung cấp thông tin chi tiết: chất liệu (material), thương hiệu (brand), mô tả ngắn (short_description), size, màu sắc, giá.
            - Khi mô tả sản phẩm, hãy sử dụng thông tin từ short_description và description nếu có.
            - Sử dụng link hãy gửi kèm thẻ <a> để truy cập thay vì text.
            - Khi người dùng hỏi còn hàng không chỉ trả lời những size và màu có available_stock lớn hơn 0.
            - So sánh, tư vấn dựa trên sản phẩm đã biết.
            - Khi khách hỏi tư vấn chi tiết hay hỏi rõ thông tin sản phẩm hãy gửi kèm link sản phẩm theo định dạng http://127.0.0.1:8000/product/{slug}
            2. Tương tác thông minh:
            - Khi khách hỏi 'cái nào đẹp hơn' hay đại loại là so sánh sản phẩm,
            hãy phân tích và so sánh thông tin sản phẩm dựa vào thông tin lưu trong context → So sánh các sản phẩm đã show
            - Khi hỏi về giá → Tham khảo giá các sản phẩm trong context.
            - Khi hỏi về size → Dựa vào sản phẩm đã đề cập.
            - Gợi ý combo, phối đồ từ các sản phẩm có sẵn.
            3. Khi được hỏi về cách đặt hàng:
            - Hãy hướng dẫn step by step từ bước từ tìm kiếm tên sản phẩm,
            chọn vào sản phẩm, chọn size và số lượng, nhấn thêm vào giỏ hàng, kiểm tra giỏ hàng và chọn thanh toán,
            nhập thông tin giao hàng và chọn phương thức thanh toán, nhấn nút thanh toán.
            4. Khi khách hỏi về quy trình đổi trả:
            - Hãy hướng dẫn khách liên hệ cửa hàng qua contact hoặc hotline để được giải đáp.
            5. Khi khách hỏi về tra cứu đơn hàng:
            - Hãy hướng dẫn khách liên hệ cửa hàng qua contact hoặc hotline để được giải đáp.
            6. Khi câu trả lời dính từ khoá trong rulebase:
            - Hãy trả lời một cách tự nhiên là bạn tìm sản phẩm hay thông tin do bắt gặp từ khoá đó.
            ### LIÊN KẾT QUAN TRỌNG:
            - Trang liên hệ: <a href='http://127.0.0.1:8000/contact'>Contacts</a>
            - Blog thời trang: <a href='http://127.0.0.1:8000/blog'>Blog</a>
            - Cửa hàng: <a href='http://127.0.0.1:8000/shop'>Shop</a>
            - Hướng dẫn chọn size: <a href='https://res.cloudinary.com/dc2zvj1u4/image/upload/v1748404290/ao/file_u0eqqq.jpg'>Hướng dẫn chọn size</a>
            ### NHỮNG ĐIỀU TUYỆT ĐỐI KHÔNG ĐƯỢC LÀM:
            ❌ Không bịa tên thương hiệu (M.O.T, ABC, XYZ...)
            ❌ Không bịa tên sản phẩm cụ thể
            ❌ Không bịa giá tiền
            ❌ Không bịa thông tin 'đang hot', 'bán chạy'
            ❌ Không nói 'sản phẩm X đang được ưa chuộng' khi không có dữ liệu
            ❌ Không bịa chương trình khuyến mãi
            ❌ Không đưa ra thông tin sản phẩm khi product_context rỗng
            ### LƯU Ý QUAN TRỌNG:
            - Luôn kiểm tra product_context trước khi đưa thông tin sản phẩm
            - Thành thật thừa nhận khi không có thông tin thay vì bịa
            - Hướng khách đến nguồn thông tin chính thức (Shop, Contact)
            - Chỉ trả lời về chính sách, dịch vụ cửa hàng khi không có dữ liệu sản phẩm
            - Từ chối trả lời câu hỏi liên quan về chính trị, tôn giáo, y tế";





    // Main message handling endpoint
    public function sendMessage(Request $request)
    {
        try {
            $userId = 'user_gemma3_newway';
            $userMessage = $request->input('message');
            // Lưu lịch sử chat với key "chat_history:[userId]"
            $historyKey = "chat_history:$userId";
            // Lưu ngữ cảnh sản phẩm với key có dạng "product_context:[userId]"
            $productContextKey = "product_context:$userId";
            $maxMessages = 50;
            $summarizeThreshold = 50;
            Log::info('Chatbot request received', ['message' => $userMessage]);
            // Kiểm tra câu hỏi có rơi vào các trường hợp đặc biệt không
            $specialResponse = $this->handleSpecialCases($userMessage, $userId);
            if ($specialResponse) {
                Log::info('Returning special response', $specialResponse);
                return response()->json([
                    'reply_data' => $specialResponse,
                    'reply' => $specialResponse['content'] ?? $specialResponse['message'] ?? ''
                ]);
            }


            // $followUpResponse = $this->handleFollowUpQuestions($userMessage, $userId);
            // if ($followUpResponse) {
            //     Log::info('Returning follow-up product detail', $followUpResponse);
            //     return response()->json([
            //         'reply_data' => $followUpResponse,
            //         'reply' => $followUpResponse['content'] ?? ''
            //     ]);
            // }
            // Handle "more products" request
            $moreProductsResponse = $this->handleMoreProductsRequest($userMessage, $userId);
            if ($moreProductsResponse) {
                Log::info('Returning more products response', $moreProductsResponse);
                return response()->json([
                    'reply_data' => $moreProductsResponse,
                    'reply' => $moreProductsResponse['content'] ?? $moreProductsResponse['message'] ?? ''
                ]);
            }
            // Get and process chat history
            $historyRaw = Redis::lrange($historyKey, 0, -1);
            $history = array_map('json_decode', $historyRaw);
            // Summarize if needed
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
            // Get recent messages
            $recentRaw = Redis::lrange($historyKey, -20, -1);
            $recentHistory = array_map('json_decode', $recentRaw);
            // Build system prompt with product context
            $fullSystemPrompt = $this->buildSystemPromptWithProductContext($userId);
            // Build chat prompt
            $chatPrompt = $this->buildChatPrompt($recentHistory, $fullSystemPrompt, $userMessage);
            // Call AI GEMMA3
            $payload = [
                'model' => env('GEMMA_MODEL'),
                'prompt' => $chatPrompt,
                'stream' => env('GEMMA_STREAM'),
                // 'temperature' => env('GEMMA_TEMPERATURE')
            ];
            $response = Http::timeout(60)->post(env('OLLAMA_API_URL'), $payload);
            Log::info('Chatbot request sent', $payload);
            // Log::info('Chatbot request sent', ['payload' => $payload]);
            if (!$response->successful()) {
                throw new Exception('Failed to connect to OLLama: ' . $response->status());
            }
            $data = $response->json();
            $replyRaw = $data['response'] ?? '[Không có phản hồi từ AI]';
            $reply = preg_replace('/^ASSISTANT:\s*/i', '', $replyRaw);
            Log::info('Chatbot response received', ['reply' => $reply]);
            $reply = $this->processContentWithImages($reply);
            // Save to history
            Redis::rpush($historyKey, json_encode(['role' => 'user', 'message' => $userMessage]));
            Redis::rpush($historyKey, json_encode(['role' => 'assistant', 'message' => $reply]));
            Redis::ltrim($historyKey, -$maxMessages, -1);
            Redis::expire($historyKey, 60 * 60 * 24);
            return response()->json(['reply' => $reply]);
        } catch (Exception $e) {
            Log::error('Chatbot API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Xin lỗi, đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
                'technical_message' => env('APP_DEBUG') ? $e->getMessage() : null,
                'reply' => 'Xin lỗi, hiện tại hệ thống đang gặp sự cố. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ.'
            ], 500);
        }
    }

    protected function handleMoreProductsRequest(string $message, string $userId): ?array
    {
        $message = mb_strtolower(trim($message));

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

        $productContextKey = "product_context:$userId";
        $contextRaw = Redis::lrange($productContextKey, 0, -1);

        if (empty($contextRaw)) {
            return null;
        }

        $lastProduct = json_decode(end($contextRaw));
        $lastQuery = $lastProduct->query ?? '';

        if (empty($lastQuery)) {
            return null;
        }

        $productKeywords = $this->detectProductKeywords($lastQuery);

        if (empty($productKeywords)) {
            return null;
        }

        $products = $this->getProductRecommendations(
            $productKeywords['category'],
            $productKeywords['keywords'],
            10,
            5
        );

        if (!empty($products)) {
            $this->saveProductsToContext($userId, $products, $lastQuery);
            return $this->formatProductResponse($products, true);
        }

        return [
            'type' => 'text',
            'content' => "Hiện mình không tìm thấy thêm sản phẩm nào tương tự. Bạn có muốn xem sản phẩm khác không ạ?"
        ];
    }

    protected function buildSystemPromptWithProductContext(string $userId): string
    {
        $productContextKey = "product_context:$userId";
        $productContextRaw = Redis::lrange($productContextKey, 0, -1);

        $fullSystemPrompt = $this->defaultPrompt;

        if (!empty($productContextRaw)) {
            $productContext = array_map('json_decode', $productContextRaw);
            $contextText = "\n\n### SẢN PHẨM ĐÃ THẢO LUẬN TRONG PHIÊN:\n";

            foreach ($productContext as $item) {
                $contextText .= "- {$item->name}: {$item->price} - {$item->link}\n";
                if (!empty($item->details)) {
                    $contextText .= "  Chi tiết: {$item->details}\n";
                }
                if (!empty($item->stock_summary)) {
                    $contextText .= "  Tình trạng: {$item->stock_summary}\n";
                }
            }
            $contextText .= "\nHãy sử dụng thông tin này để tư vấn, so sánh và gợi ý cho khách hàng.\n";

            $fullSystemPrompt .= $contextText;
        }

        // Thêm hướng dẫn xử lý câu hỏi tồn kho
        $fullSystemPrompt .= "\n\n### HƯỚNG DẪN XỬ LÝ CÂU HỎI TỒN KHO:\n"
            . "- Khi khách hỏi 'còn hàng không' hoặc về tình trạng kho:\n"
            . "  + Kiểm tra thông tin variants trong context sản phẩm\n"
            . "  + Chỉ liệt kê các size/màu có available_stock > 0\n"
            . "  + Nếu hết hàng, đề nghị xem sản phẩm tương tự\n"
            . "  + Khi hỏi cụ thể về size, chỉ trả lời thông tin của size đó"
            . "- Khi khách hỏi về phối đồ:\n"
            . "  + Đưa ra 3-4 gợi ý phối đồ phù hợp\n"
            . "  + Kèm hình ảnh minh họa nếu có";

        return $fullSystemPrompt;
    }

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


    protected function handleFollowUpQuestions(string $message, string $userId): ?array
    {
        $productContextKey = "product_context:$userId";
        $contextRaw = Redis::lrange($productContextKey, 0, -1);

        if (empty($contextRaw)) return null;

        $lastProduct = json_decode(end($contextRaw));
        $message = mb_strtolower(trim($message));

        // Xử lý các câu hỏi follow-up về sản phẩm
        if (preg_match('/^(chi tiết|thông tin|cho xem|sản phẩm|sp)\s*(\d+|.*)?$/ui', $message, $matches)) {
            $productRef = trim($matches[2] ?? '');

            // Nếu không chỉ rõ sản phẩm, dùng sản phẩm cuối cùng
            if (empty($productRef)) {
                return $this->formatProductDetailResponse($lastProduct);
            }

            // Nếu chỉ số sản phẩm (1, 2...)
            if (is_numeric($productRef)) {
                $index = (int)$productRef - 1;
                if (isset($contextRaw[$index])) {
                    return $this->formatProductDetailResponse(json_decode($contextRaw[$index]));
                }
            }

            // Nếu chỉ tên sản phẩm
            foreach ($contextRaw as $item) {
                $product = json_decode($item);
                if (str_contains(mb_strtolower($product->name), mb_strtolower($productRef))) {
                    return $this->formatProductDetailResponse($product);
                }
            }

            // Nếu không tìm thấy sản phẩm cụ thể
            return [
                'type' => 'text',
                'content' => "Mình không tìm thấy sản phẩm '$productRef' trong danh sách đã hiển thị. Bạn có thể xem lại các sản phẩm ở trên hoặc yêu cầu cụ thể hơn."
            ];
        }

        return null;
    }


    // Xử lý các trường hợp đặc biệt
    protected function handleSpecialCases(string $message, string $userId): ?array
    {
        $message = mb_strtolower(trim($message));


        // Check for product keywords first
        $productKeywords = $this->detectProductKeywords($message);
        if (!empty($productKeywords)) {
            // Handle price intent first
            if (isset($productKeywords['intent'])) {
                $products = $this->getProductRecommendations(
                    $productKeywords['category'] ?? null,
                    $productKeywords['keywords'] ?? [],
                    5,
                    0,
                    $productKeywords['intent'],
                    $productKeywords['min_price'] ?? null,
                    $productKeywords['max_price'] ?? null
                );

                if (!empty($products)) {
                    $this->saveProductsToContext($userId, $products, $message);
                    return $this->formatPriceIntentResponse($products, $productKeywords);
                } else {
                    return [
                        'type' => 'text',
                        'content' => "Xin lỗi, không tìm thấy sản phẩm phù hợp với yêu cầu '{$productKeywords['matched_term']}' của bạn."
                    ];
                }
            }

            // Regular product handling
            $products = $this->getProductRecommendations($productKeywords['category'], $productKeywords['keywords']);
            if (!empty($products)) {
                $this->saveProductsToContext($userId, $products, $message);
                return $this->formatProductResponse($products);
            } else {
                return ['type' => 'text', 'content' => "Xin lỗi, hiện tại mình chưa tìm thấy sản phẩm '{$productKeywords['matched_term']}' phù hợp."];
            }
        }

        // Xử lý câu hỏi về tình trạng tồn kho
        if (preg_match('/(còn hàng không|hết hàng chưa|cho xem kho hàng|tồn kho|còn size|size nào còn)\s*(.*)/ui', $message, $matches)) {
            return $this->handleStockInquiry($message, $userId);
        }

        // Special cases
        if (preg_match('/^(bảng size|hướng dẫn chọn size|size guide|size chart)\??$/ui', $message)) {
            return [
                'type' => 'text_with_image',
                'content' => "Đây là bảng hướng dẫn chọn size của TST Fashion:",
                'image_url' => 'https://res.cloudinary.com/dc2zvj1u4/image/upload/v1748404290/ao/file_u0eqqq.jpg'
            ];
        }

        if (preg_match('/^(giờ mở cửa|thời gian làm việc)( của cửa hàng)?\??$/u', $message)) {
            return ['type' => 'text', 'content' => "Cửa hàng mở cửa từ 8:00 - 22:00 hàng ngày."];
        }

        if (preg_match('/^(phí vận chuyển|ship hàng|phí ship)( là bao nhiêu)?\??$/u', $message)) {
            return ['type' => 'text', 'content' => "Hiện tại chúng tôi miễn phí vận chuyển cho đơn hàng từ 500.000đ trở lên. Đơn dưới 500.000đ phí ship là 30.000đ."];
        }

        // Xử lý câu hỏi về sale
        if (preg_match('/đang sale|đang giảm giá|khuyến mãi|giảm giá|discount/ui', $message)) {
            $discountedProducts = $this->getDiscountedProducts(5);

            if (empty($discountedProducts)) {
                return [
                    'type' => 'text',
                    'content' => "Hiện không có sản phẩm nào đang giảm giá, nhưng bạn có thể tham khảo các sản phẩm mới nhất tại: <a href='http://127.0.0.1:8000/shop'>Shop</a>"
                ];
            }

            return $this->formatProductResponse($discountedProducts);
        }

        if (preg_match('/\b(xấu|dở|tệ|chán|đểu|ngu|tồi|dốt|kém)\b/u', $message)) {
            return ['type' => 'text', 'content' => "Xin lỗi nếu sản phẩm chưa làm bạn hài lòng. Mình có thể giúp gì để cải thiện trải nghiệm mua sắm của bạn không ạ?"];
        }

        if (preg_match('/^(hôm nay là ngày mấy|ngày hôm nay|hôm nay ngày mấy)\??$/u', $message)) {
            return ['type' => 'text', 'content' => "Hôm nay là ngày: " . date('d/m/Y')];
        }

        if (preg_match('/^(mấy giờ rồi|giờ hôm nay|bây giờ là mấy giờ)\??$/u', $message)) {
            return ['type' => 'text', 'content' => "Bây giờ là: " . date('H:i')];
        }

        return null;
    }




    protected function handleStockInquiry(string $message, string $userId): ?array
    {
        $productContextKey = "product_context:$userId";
        $contextRaw = Redis::lrange($productContextKey, 0, -1);

        if (empty($contextRaw)) {
            return [
                'type' => 'text',
                'content' => "Hiện mình chưa có thông tin sản phẩm nào trong phiên chat..."
            ];
        }

        // Xác định sản phẩm cụ thể mà người dùng hỏi
        $targetProduct = null;
        $message = mb_strtolower(trim($message));

        // Kiểm tra xem người dùng có đề cập đến tên sản phẩm không
        foreach ($contextRaw as $itemRaw) {
            $item = json_decode($itemRaw);
            $productNameLower = mb_strtolower($item->name);

            // Nếu tìm thấy tên sản phẩm trong câu hỏi
            if (str_contains($message, $productNameLower)) {
                $targetProduct = $item;
                break;
            }
        }

        // Nếu không tìm thấy sản phẩm cụ thể, dùng sản phẩm cuối cùng
        if (!$targetProduct) {
            $targetProduct = json_decode(end($contextRaw));
        }

        // Lấy danh sách size từ database
        $availableSizes = Cache::remember('product_sizes', 3600, function () {
            return ProductVariant::select('size')
                ->distinct()
                ->orderByRaw("FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')")
                ->pluck('size')
                ->toArray();
        });

        $sizePattern = implode('|', array_map('preg_quote', $availableSizes));
        if (preg_match('/size\s*(' . $sizePattern . ')/i', $message, $matches)) {
            $sizeInquiry = strtoupper($matches[1]);
        }

        // Xử lý thông tin tồn kho
        if (empty($targetProduct->variants)) {
            return [
                'type' => 'text',
                'content' => "Hiện mình không có thông tin tồn kho chi tiết cho sản phẩm này..."
            ];
        }

        $availableVariants = array_filter($targetProduct->variants, function ($variant) use ($sizeInquiry) {
            // Nếu có yêu cầu size cụ thể thì lọc theo size
            if ($sizeInquiry) {
                return ($variant->available_stock > 0) &&
                    (strtoupper($variant->size) === $sizeInquiry);
            }
            return $variant->available_stock > 0;
        });

        if (empty($availableVariants)) {
            $response = "Hiện sản phẩm {$targetProduct->name}";
            $response .= $sizeInquiry ? " đã hết hàng size {$sizeInquiry}" : " đã hết hàng";
            $response .= ". Bạn có muốn xem sản phẩm tương tự không ạ?";

            return ['type' => 'text', 'content' => $response];
        }

        // Format response
        $variantInfo = array_map(function ($variant) {
            $color = $variant->color ?? 'đang cập nhật';
            return "Size {$variant->size} - Màu {$color} (Còn {$variant->available_stock})";
        }, $availableVariants);

        $response = "Sản phẩm {$targetProduct->name} hiện còn:\n- ";
        $response .= implode("\n- ", $variantInfo);
        $response .= "\nBạn quan tâm size và màu nào ạ?";

        return ['type' => 'text', 'content' => $response];
    }

    protected function detectSpecificProductInquiry(string $message, array $context): ?string
    {
        // Kiểm tra tên sản phẩm cụ thể trong message
        $productNames = array_map(function ($item) {
            return mb_strtolower(trim($item->name));
        }, $context);

        foreach ($productNames as $name) {
            if (str_contains(mb_strtolower($message), $name)) {
                return $name;
            }

            // Kiểm tra từ khóa chính
            $mainKeyword = explode(' ', $name)[0] ?? '';
            if (str_contains(mb_strtolower($message), $mainKeyword)) {
                return $name;
            }
        }

        return null;
    }



    protected function detectProductKeywords(string $message): array
    {
        $message = mb_strtolower(trim($message));

        $priceIntent = $this->detectPriceIntent($message);
        if ($priceIntent) {
            return $priceIntent;
        }

        $productMap = $this->getProductCategoriesWithKeywords();
        Log::info('Product keywords map', $productMap);

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

    protected function getProductCategoriesWithKeywords(): array
    {
        $cacheKey = 'product_categories_keywords_map';
        $cacheDuration = now()->addHours(6); // Cache 6 tiếng

        return Cache::remember($cacheKey, $cacheDuration, function () {
            $productMap = [];

            // Lấy tất cả danh mục
            $categories = Category::with(['products' => function ($query) {
                $query->select('id', 'category_id', 'tags')
                    ->whereNotNull('tags');
            }])->get();

            foreach ($categories as $category) {
                $categoryName = mb_strtolower($category->category_name);

                // Tạo mảng keywords từ tags của các sản phẩm thuộc category
                $keywords = $category->products->flatMap(function ($product) {
                    return array_map('trim', explode(',', $product->tags));
                })->filter()->unique()->values()->all();

                // Thêm chính tên category vào keywords
                $keywords = array_unique(array_merge([$categoryName], $keywords));

                // Thêm vào productMap
                $productMap[$categoryName] = [
                    'category' => $category->category_name,
                    'keywords' => $keywords
                ];

                // Thêm các biến thể ngắn của tên category (nếu cần)
                if (str_contains($categoryName, 'áo')) {
                    $shortName = str_replace('áo ', '', $categoryName);
                    $productMap[$shortName] = $productMap[$categoryName];
                }
            }

            // Thêm các từ khóa đặc biệt (nếu cần)
            $productMap['thun'] = $productMap['áo thun'] ?? null;
            $productMap['sơ mi'] = $productMap['áo sơ mi'] ?? null;

            return array_filter($productMap);
        });
    }


    protected function detectCategory(string $message): ?string
    {
        $productMap = $this->getProductCategoriesWithKeywords();

        $sortedKeys = array_keys($productMap);
        usort($sortedKeys, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($sortedKeys as $keyword) {
            if (str_contains(mb_strtolower($message), $keyword)) {
                return $productMap[$keyword]['category'] ?? null;
            }
        }

        return null;
    }


    protected function detectPriceIntent(string $message): ?array
    {
        $category = $this->detectCategory($message);

        Log::info('category: ' . $category);
        // 1. Cheapest
        if (str_contains($message, 'rẻ nhất') || str_contains($message, 'giá rẻ nhất') || str_contains($message, 'sản phẩm giá rẻ') || preg_match('/rẻ\s*(\d+[\d\.,]*)[k]?/u', $message)) {
            return [
                'intent' => 'cheapest',
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => $category ? "top {$category} rẻ nhất" : "top sản phẩm rẻ nhất"
            ];
        }

        // 2. Most expensive
        if (str_contains($message, 'đắt nhất') || str_contains($message, 'giá cao nhất')) {
            return [
                'intent' => 'most_expensive',
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => $category ? "top {$category} đắt nhất" : "top sản phẩm đắt nhất"
            ];
        }

        // 3. Under price
        if (preg_match('/dưới\s*(\d+[\d\.,]*)[k]?/u', $message, $matches)) {
            $price = (int)str_replace(['.', ','], '', $matches[1]);
            if (str_contains($matches[1], 'k') || $price < 10000) {
                $price *= 1000;
            }

            return [
                'intent' => 'under_price',
                'max_price' => $price,
                'category' => $category,
                'keywords' => $category ? [$category] : [],
                'matched_term' => "dưới " . number_format($price) . "đ"
            ];
        }

        // 4. Price range
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

    protected function saveProductsToContext(string $userId, array $products, string $userQuery): void
    {
        $productContextKey = "product_context:$userId";
        // Redis::del($productContextKey);

        foreach ($products as $product) {
            $variants = $product['variants'] ?? [];

            if (!$this->isProductInContext($userId, $product['name'])) {
                $contextItem = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'original_price' => $product['original_price'] ?? null,
                    'discount_percent' => $product['discount_percent'] ?? null,
                    'link' => $product['link'],
                    'image' => $product['image_url'] ?? $product['image'],
                    'query' => $userQuery,
                    'timestamp' => time(),
                    'details' => $this->extractProductDetails($product),
                    'variants' => $variants,
                    'stock_summary' => $this->generateStockSummary($variants),
                    'material' => $product['material'] ?? null,
                    'description' => $product['description'] ?? null,
                    'short_description' => $product['short_description'] ?? null,
                    'brand' => $product['brand'] ?? null
                ];
                Redis::rpush($productContextKey, json_encode($contextItem));
            }
        }

        Redis::expire($productContextKey, 60 * 60 * 2);
    }

    protected function generateStockSummary(array $variants): string
    {
        if (empty($variants)) return 'Thông tin tồn kho chưa cập nhật';

        $available = array_filter($variants, function ($v) {
            return ($v['available_stock'] ?? 0) > 0;
        });
        if (empty($available)) return 'Đã hết hàng';

        $sizes = array_unique(array_column($available, 'size'));
        $colors = array_unique(array_column($available, 'color'));

        return 'Còn hàng size: ' . implode(', ', $sizes) .
            ' | Màu: ' . implode(', ', $colors);
    }

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

    protected function extractProductDetails(array $product): string
    {
        $details = [];
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
        // Thêm thông tin material nếu có
        if (!empty($product['material'])) {
            $details[] = 'Chất liệu: ' . $product['material'];
        }

        // Thêm thông tin brand nếu có
        if (!empty($product['brand'])) {
            $details[] = 'Thương hiệu: ' . $product['brand'];
        }

        // Thêm short description nếu có
        if (!empty($product['short_description'])) {
            $details[] = $product['short_description'];
        }

        if (!empty($product['variants'])) {
            $variantDetails = [];
            foreach ($product['variants'] as $variant) {
                if (($variant['available_stock'] ?? 0) > 0) {
                    $variantDetails[] = sprintf(
                        "%s - %s (Còn %d)",
                        $variant['size'],
                        $variant['color'],
                        $variant['available_stock']
                    );
                }
            }

            if (!empty($variantDetails)) {
                $details[] = 'Size & màu còn hàng: ' . implode(', ', $variantDetails);
            } else {
                $details[] = 'Đã hết hàng';
            }
        }

        return implode(', ', $details);
    }

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

        $response = Http::timeout(60)->post(env('OLLAMA_API_URL'), $payload);

        if (!$response->successful()) {
            return '';
        }

        $data = $response->json();
        return trim($data['response'] ?? '');
    }

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

        $query = Product::with([
            'category',
            'discount' => function ($query) use ($now) {
                $query->where('status', 'active')
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            },
            'ProductVariants' => function ($query) {
                $query->select('id', 'product_id', 'size', 'color', 'available_stock')
                    ->where('active', 1)
                    ->where('available_stock', '>', 0); // Chỉ lấy các variant còn hàng
            }
        ])->select('id', 'product_name', 'price', 'slug', 'image', 'discount_id', 'material', 'description', 'short_description', 'brand')
            ->where('status', 1);

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category_name', 'like', '%' . $category . '%');
            });

            // Thêm điều kiện tìm trong tags nếu có keywords
            if (!empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('tags', 'like', '%' . $keyword . '%')
                            ->orWhere('product_name', 'like', '%' . $keyword . '%');
                    }
                });
            }
        } elseif (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('product_name', 'like', '%' . $keyword . '%');
                }
            });
        }

        // Price filters
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        // Order by intent
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
                $discountPercent = $product->discount->percent_discount;
                $discountCode = $product->discount->code;
                $price = $price * (1 - $discountPercent);
            }

            // Get variants
            $variants = [];
            if ($product->relationLoaded('ProductVariants')) {
                foreach ($product->ProductVariants as $variant) {
                    $variants[] = [
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'available_stock' => $variant->available_stock
                    ];
                }
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
                'image_url' => $product->image,
                'has_discount' => !is_null($discountPercent),
                'variants' => $variants,
                'material' => $product->material ?? null,
                'description' => $product->description ?? null,
                'short_description' => $product->short_description ?? null,
                'brand' => $product->brand ?? null,
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
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $formattedProducts = [];
        foreach ($products as $product) {
            $price = $product->price;
            $originalPrice = $price;
            $discountPercent = $product->discount->percent_discount;
            $price = $price * (1 - $discountPercent);

            $formattedProducts[] = [
                'name' => $product->product_name,
                'price' => $this->formatPrice($price),
                'original_price' => $this->formatPrice($originalPrice),
                'discount_percent' => $discountPercent,
                'discount_code' => $product->discount->code,
                'discount_name' => $product->discount->name,
                'link' => '/product/' . $product->slug,
                'image' => $product->image,
                'image_url' => $product->image,
                'has_discount' => true
            ];
        }

        return $formattedProducts;
    }

    protected function formatPriceIntentResponse(array $products, array $intent): array
    {
        switch ($intent['intent']) {
            case 'cheapest':
                return [
                    'type' => 'product_list',
                    'intro_message' => "Đây là top 3 sản phẩm {$intent['matched_term']}:",
                    'products' => array_slice($products, 0, 3),
                    'outro_message' => "Bạn muốn xem chi tiết sản phẩm nào ạ?"
                ];

            case 'most_expensive':
                return [
                    'type' => 'product_list',
                    'intro_message' => "Đây là top 3 sản phẩm {$intent['matched_term']}:",
                    'products' => array_slice($products, 0, 3),
                    'outro_message' => "Bạn muốn xem chi tiết sản phẩm nào ạ?"
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

    protected function formatProductResponse(array $products, bool $isMoreRequest = false): array
    {
        $productData = [];
        foreach ($products as $product) {
            $discountPercentDisplay = $product['discount_percent'] ? $product['discount_percent'] : 0;

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

    protected function formatPrice($price)
    {
        return number_format($price, 0, ',', '.') . 'đ';
    }

    protected function processContentWithImages(string $content): string
    {
        // Xử lý các URL ảnh đã bị mã hóa HTML
        $content = html_entity_decode($content);

        // Pattern để tìm URL ảnh
        $imagePattern = '/(https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp|avif))/i';

        // Thay thế URL ảnh bằng thẻ img
        $processedContent = preg_replace_callback($imagePattern, function ($matches) {
            $imageUrl = htmlspecialchars($matches[0], ENT_QUOTES);
            return "<img src='{$imageUrl}' alt='Hình ảnh sản phẩm' style='max-width: 300px; height: auto; border-radius: 8px; margin: 10px 0; display: block;'>";
        }, $content);

        return $processedContent;
    }


    public function clearHistory(Request $request)
    {
        try {
            $userId = 'user_gemma3_newway'; // Sử dụng cùng userId như các phương thức khác
            $historyKey = "chat_history:$userId";
            $productContextKey = "product_context:$userId";

            // Xóa cả lịch sử chat và context sản phẩm
            Redis::del($historyKey);
            Redis::del($productContextKey);

            return response()->json([
                'success' => true,
                'message' => 'Lịch sử chat đã được xóa'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa lịch sử: ' . $e->getMessage()
            ], 500);
        }
    }


    protected function formatProductDetailResponse($product)
    {
        $imageTag = '';
        if (!empty($product->image)) {
            $imageTag = "<div class='text-center mb-2'><img src='{$product->image}' alt='Ảnh sản phẩm' class='img-fluid rounded border' style='max-width: 300px; object-fit: contain;'></div>";
        }
        $content = $imageTag
            . "<b>{$product->name}</b><br>"
            . (!empty($product->brand) ? "Thương hiệu: {$product->brand}<br>" : "")
            . (!empty($product->material) ? "Chất liệu: {$product->material}<br>" : "")
            . (!empty($product->short_description) ? "{$product->short_description}<br>" : "")
            . (!empty($product->description) ? "{$product->description}<br>" : "")
            . (!empty($product->stock_summary) ? "Tình trạng: {$product->stock_summary}<br>" : "")
            . ($product->link ? "<a href='{$product->link}' target='_blank'>Xem chi tiết sản phẩm</a>" : "");
        return [
            'type' => 'product_detail',
            'content' => $content,
            'image_url' => $product->image ?? null,
            'link' => $product->link ?? null,
        ];
    }
}
