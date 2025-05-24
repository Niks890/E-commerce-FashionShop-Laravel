<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log; // Import Facade Log
use Illuminate\Support\Facades\DB; // Import Facade DB

class ChatBotApiController extends Controller
{
    // CÁCH CŨ DÙNG CONTEXT (đã comment)
    // public function sendMessage(Request $request)
    // {
    //     // ... (giữ nguyên hoặc xóa nếu không dùng)
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
        $contextKey = "chat_context_data:$userId"; // Key mới cho ngữ cảnh hội thoại
        $maxMessages = 50;
        $summarizeThreshold = 50; // Khi số tin nhắn vượt ngưỡng thì tóm tắt

        // Lấy ngữ cảnh hiện tại từ Redis
        $context = Redis::get($contextKey);
        $context = $context ? json_decode($context, true) : ['step' => 'initial', 'product_type' => null, 'color' => null];

        // Xử lý các trường hợp đặc biệt trước khi gửi cho AI
        $specialResponse = $this->handleSpecialCases($userMessage, $contextKey);
        if ($specialResponse) {
            // Cập nhật ngữ cảnh nếu có thay đổi từ hàm handleSpecialCases
            Redis::set($contextKey, json_encode($context));
            Redis::expire($contextKey, 60 * 60 * 24); // Đặt TTL cho context

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
        Redis::expire($contextKey, 60 * 60 * 24); // Đặt TTL cho context

        return response()->json([
            'reply' => $reply
        ]);
    }

    // Hàm xử lý trường hợp đặc biệt
    protected function handleSpecialCases(string $message, string $contextKey): ?array
    {
        $message = mb_strtolower(trim($message));
        $context = Redis::get($contextKey);
        $context = $context ? json_decode($context, true) : ['step' => 'initial', 'product_type' => null, 'color' => null];

        // Thêm các hàm xử lý từ đoạn code bạn cung cấp
        $response = $this->handleExit($message, $contextKey);
        if ($response) return ['type' => 'text', 'content' => $response];

        $response = $this->handleProductSuggestion($message, $contextKey);
        if ($response) return $response; // Hàm này đã trả về mảng

        $response = $this->handleProductDiscount($message, $contextKey);
        if ($response) return $response; // Hàm này đã trả về mảng

        $response = $this->handleProductColor($message, $contextKey);
        if ($response) return $response; // Hàm này đã trả về mảng

        $response = $this->handleConversation($context, $message, $contextKey);
        if ($response) return ['type' => 'text', 'content' => $response];

        $response = $this->handleProductSelection($context, $message, $contextKey);
        if ($response) return $response; // Hàm này đã trả về mảng

        // Giữ nguyên các case đặc biệt hiện có
        // Hỏi về giờ mở cửa
        if (str_contains($message, 'giờ mở cửa') || str_contains($message, 'thời gian làm việc')) {
            return ['type' => 'text', 'content' => "Cửa hàng mở cửa từ 8:00 - 22:00 hàng ngày."];
        }

        // Hỏi về chính sách vận chuyển
        if (str_contains($message, 'phí vận chuyển') || str_contains($message, 'ship hàng')) {
            return ['type' => 'text', 'content' => "Hiện tại chúng tôi miễn phí vận chuyển cho đơn hàng từ 500.000đ trở lên. Đơn dưới 500.000đ phí ship là 25.000đ."];
        }

        // Hỏi về khuyến mãi (đã có handleProductDiscount)
        // if (str_contains($message, 'khuyến mãi') || str_contains($message, 'giảm giá') || str_contains($message, 'sale')) {
        //     return ['type' => 'text', 'content' => "Hiện đang có chương trình giảm 20% cho áo thun và 15% cho quần jeans. Bạn có thể xem chi tiết tại <a href='http://127.0.0.1:8000/promotions'>đây</a>."];
        // }

        // Hỏi về hướng dẫn chọn size
        if (str_contains($message, 'chọn size') || str_contains($message, 'hướng dẫn size')) {
            return ['type' => 'text', 'content' => "Bạn có thể tham khảo hướng dẫn chọn size tại <a href='http://127.0.0.1:8000/size-guide'>đây</a>. Hoặc cho mình biết chiều cao/cân nặng để tư vấn cụ thể nhé!"];
        }

        // Từ ngữ không phù hợp
        if (preg_match('/\b(xấu|dở|tệ|chán|đểu|ngu)\b/u', $message)) {
            return ['type' => 'text', 'content' => "Xin lỗi nếu sản phẩm chưa làm bạn hài lòng. Mình có thể giúp gì để cải thiện trải nghiệm mua sắm của bạn không ạ?"];
        }

        // Xử lý truy vấn sản phẩm tổng quát nếu không có case đặc biệt nào trước đó bắt được
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

    protected function formatProductResponse(array $products, string $title = "Mình xin gợi ý một số sản phẩm dành cho bạn:"): array
    {
        // Thay vì trả về một chuỗi, bây giờ chúng ta sẽ trả về một mảng chứa thông tin sản phẩm
        // để frontend có thể render tùy chỉnh.
        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'name' => $product->product_name, // Sử dụng ->product_name
                'price' => number_format($product->price, 0, ',', '.') . 'đ', // Sử dụng ->price
                'link' => route('sites.productDetail', ['slug' => $product->slug]), // Sử dụng ->slug
                'image_url' => asset("uploads/{$product->image}"), // Sử dụng ->image
                'stock' => $product->stock,
                'color' => $product->color ?? '',
                'size' => $product->size ?? '',
                'discount' => $product->discount ?? null, // Thêm discount nếu có
            ];
        }

        return [
            'type' => 'product_list',
            'intro_message' => $title,
            'products' => $productData,
            'outro_message' => "\nBạn muốn xem chi tiết sản phẩm nào ạ?",
        ];
    }

    // --- Các hàm bạn cung cấp, đã điều chỉnh để dùng Redis thay session ---

    private function handleExit($message, $contextKey)
    {
        if (preg_match('/\b(không mua|thoát|hủy|bye|tạm biệt|ko mua)\b/i', $message)) {
            Redis::del($contextKey); // Xóa context trong Redis
            return "Cảm ơn bạn đã ghé thăm TST Fashion Shop! Nếu cần tư vấn thêm, hãy nhắn tin nhé! 😊";
        }
        return null;
    }

    private function handleProductSuggestion($message, $contextKey)
    {
        if (preg_match('/\b(gợi ý vài sản phẩm|sản phẩm nổi bật|có gì hot|vài cái sản phẩm đi|gợi ý|đề xuất)\b/i', $message)) {
            return $this->getProductList();
        }
        return null;
    }

    private function handleProductQuery($message, $contextKey)
    {
        $categories = [
            // 'áo' => 'áo', // Có thể gây trùng lặp với các loại áo cụ thể
            'áo thun' => 'áo thun',
            'áo thu' => 'áo thu',
            't-shirt' => 'áo thun',
            'áo sơ mi' => 'áo sơ mi',
            'áo hoodie' => 'áo hoodie',
            // 'quần' => 'quần', // Có thể gây trùng lặp với các loại quần cụ thể
            'quần jean' => 'quần jean',
            'quần hoodie' => 'quần hoodie',
            'giày' => 'giày',
            'sneaker' => 'giày',
            'mũ' => 'mũ',
            'hoodie' => 'hoodie',
            'váy' => 'váy',
            'phụ kiện' => 'phụ kiện'
        ];

        foreach ($categories as $keyword => $category) {
            if (stripos($message, $keyword) !== false) {
                Log::info("Đã nhận diện loại sản phẩm: " . $category);
                return $this->queryProductsByType($category, $contextKey);
            }
        }
        return null;
    }

    private function handleProductColor($message, $contextKey)
    {
        $colors = [
            'đen' => 'đen',
            'trắng' => 'trắng',
            'xanh' => 'xanh',
            'đỏ' => 'đỏ',
            'vàng' => 'vàng',
            'tím' => 'tím',
            'hồng' => 'hồng',
            'xám' => 'xám',
            'nâu' => 'nâu'
        ];
        foreach ($colors as $keyword => $color) {
            if (stripos($message, $keyword) !== false) {
                Log::info("Đã nhận diện màu sản phẩm: " . $color);
                // Lưu màu vào context để các bước sau có thể dùng
                $context = Redis::get($contextKey);
                $context = $context ? json_decode($context, true) : [];
                $context['color'] = $color;
                Redis::set($contextKey, json_encode($context));

                return $this->queryProductsByType(null, $contextKey, $color); // Truy vấn sản phẩm theo màu
            }
        }
        return null;
    }

    private function handleProductDiscount($message, $contextKey)
    {
        if (preg_match('/\b(khuyến mãi|sale|giảm giá|khuyen mai|giam gia|chương trình)\b/i', $message)) {
            return $this->getProductDiscountList();
        }
        return null;
    }

    private function handleConversation(array $context, string $message, string $contextKey)
    {
        if (isset($context['step']) && $context['step'] === 'awaiting_color') {
            $validColors = ['đen', 'trắng', 'xanh', 'đỏ', 'vàng', 'tím', 'hồng', 'xám', 'nâu'];
            if (in_array($message, $validColors)) {
                $context['color'] = $message;
                $context['step'] = 'checking_stock';
                Redis::set($contextKey, json_encode($context));

                return "Bạn muốn chọn màu $message đúng không? Hãy để mình kiểm tra kho hàng nhé!";
            }
            return "Mình chưa nhận diện được màu này. Bạn có thể chọn màu như: " . implode(', ', $validColors) . " không?";
        }
        return null;
    }

    private function handleProductSelection(array $context, string $message, string $contextKey)
    {
        // Kiểm tra xem có đang trong ngữ cảnh chờ chọn sản phẩm không
        if (isset($context['step']) && $context['step'] === 'awaiting_product_selection' && preg_match('/mẫu số (\d+)/i', $message, $matches)) {
            $index = (int)$matches[1] - 1; // Trừ 1 để khớp index trong mảng
            // Lấy danh sách sản phẩm gợi ý trước đó từ Redis
            $productsRaw = Redis::get($contextKey . '_products_list');
            $products = $productsRaw ? json_decode($productsRaw, true) : [];

            if (isset($products[$index])) {
                $product = (object) $products[$index]; // Chuyển lại thành object để truy cập thuộc tính

                // Truy vấn chi tiết sản phẩm từ database dựa vào slug hoặc id
                $productDetail = DB::table('products')
                    ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('discounts', 'products.discount_id', '=', 'discounts.id')
                    ->select('products.*', 'product_variants.color', 'categories.category_name', 'product_variants.size', 'product_variants.stock', 'discounts.name as discount')
                    ->where('products.id', $product->id) // Giả sử product có id
                    ->first();

                if ($productDetail) {
                    // Cập nhật ngữ cảnh sau khi chọn sản phẩm
                    $context['step'] = 'product_selected';
                    $context['selected_product_id'] = $product->id;
                    Redis::set($contextKey, json_encode($context));

                    return $this->formatProductResponse([$productDetail], "💡 Thông tin chi tiết về mẫu số " . ($index + 1) . ":");
                }
            }
            return ['type' => 'text', 'content' => "Mẫu số này không tồn tại. Bạn có thể kiểm tra lại danh sách mẫu không?"];
        }
        return null;
    }

    private function queryProductsByType(?string $category = null, string $contextKey, ?string $color = null)
    {
        $filters = [];
        if ($category) {
            $filters['category'] = $category;
        }
        if ($color) {
            $filters['color'] = $color;
        }

        $products = $this->fetchProducts($filters, 5);
        if ($products->isNotEmpty()) {
            // Lưu danh sách sản phẩm vào Redis để truy vấn lại khi cần
            Redis::set($contextKey . '_products_list', json_encode($products->toArray()));
            Redis::expire($contextKey . '_products_list', 60 * 5); // Lưu trong 5 phút

            // Cập nhật context để biết đang chờ lựa chọn sản phẩm
            $context = Redis::get($contextKey);
            $context = $context ? json_decode($context, true) : [];
            $context['step'] = 'awaiting_product_selection';
            Redis::set($contextKey, json_encode($context));

            $title = "🔹 Đây là một số mẫu " . ($category ?? '') . ($color ? " màu $color" : "") . " ở bên mình:";
            return $this->formatProductResponse($products->toArray(), $title);
        }
        return ['type' => 'text', 'content' => "Hiện tại chúng tôi chưa có " . ($category ?? '') . ($color ? " màu $color" : "") . " trong kho. Bạn có muốn tìm sản phẩm khác không?"];
    }

    private function fetchProducts(array $filters, int $limit = 5)
    {
        try {
            $query = DB::table('products')
                ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('discounts', 'products.discount_id', '=', 'discounts.id') // Thêm join với discounts
                ->select('products.*', 'product_variants.color', 'categories.category_name', 'product_variants.size', 'product_variants.stock', 'discounts.name as discount') // Chọn thêm discount name
                ->where('product_variants.stock', '>', 0);

            if (!empty($filters['category'])) {
                $query->where('categories.category_name', 'LIKE', "%{$filters['category']}%");
            }
            if (!empty($filters['color'])) {
                $query->where('product_variants.color', 'LIKE', "%{$filters['color']}%");
            }

            return $query->limit($limit)->get() ?? collect();
        } catch (\Exception $e) { // Bắt Exception chung
            Log::error("Lỗi truy vấn sản phẩm: " . $e->getMessage());
            return collect();
        }
    }

    private function getProductList()
    {
        $products = $this->fetchProducts([], 10);
        if ($products->isNotEmpty()) {
            return $this->formatProductResponse($products->toArray(), "🌟 Một số sản phẩm nổi bật mình tìm thấy:");
        }
        return ['type' => 'text', 'content' => "Hiện tại chưa có sản phẩm nào nổi bật. Bạn muốn tìm sản phẩm cụ thể nào không?"];
    }

    // Đã thay đổi formatProductResponse để nhận mảng thay vì Collection
    // protected function formatProductResponse($products, $title)
    // {
    //     // Hàm này đã được điều chỉnh ở trên để xử lý dữ liệu từ DB và trả về mảng cho frontend.
    // }

    private function getProductDiscountList()
    {
        $products = $this->fetchProductsDiscount(10);
        if ($products->isNotEmpty()) {
            return $this->formatProductResponse($products->toArray(), "🌟 Một số sản phẩm đang khuyến mãi mà mình tìm thấy:");
        }
        return ['type' => 'text', 'content' => "Hiện tại chưa có sản phẩm khuyến mãi nào. Bạn có muốn xem một số mẫu sản phẩm nào không?"];
    }

    private function fetchProductsDiscount($limit)
    {
        try {
            $query = DB::table('products')
                ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->join('discounts', 'products.discount_id', '=', 'discounts.id') // Chỉ lấy sản phẩm có discount
                ->select('products.*', 'discounts.name as discount', 'product_variants.color', 'categories.category_name', 'product_variants.size', 'product_variants.stock');
            return $query->limit($limit)->get() ?? collect();
        } catch (\Exception $e) {
            Log::error("Lỗi truy vấn sản phẩm khuyến mãi: " . $e->getMessage());
            return collect();
        }
    }
}
