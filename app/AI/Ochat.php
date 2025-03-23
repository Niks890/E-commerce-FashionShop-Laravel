<?php

namespace App\AI;

use App\Models\Product;
use App\Models\ProductVariant;
use Cloudstudio\Ollama\Facades\Ollama;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Ochat
{

    public function send(string $message)
    {
        try {
            $sessionKey = 'chatbot_context';
            $context = session($sessionKey, []);
            $message = mb_strtolower(trim($message));

            $handlers = [
                'handleGreeting' => [$message, $sessionKey],
                'handleExit' => [$message, $sessionKey],
                'handleProductSuggestion' => [$message, $sessionKey],
                'handleProductQuery' => [$message, $sessionKey],
                'handleProductColor' => [$message, $sessionKey],
                'handleProductDiscount' => [$message, $sessionKey],
            ];

            foreach ($handlers as $method => $params) {
                if ($response = $this->$method(...$params)) return $response;
            }

            if (!empty($context)) {
                return $this->handleProductSelection($context, $message, $sessionKey);
            }

            return $this->callOllama($message);
        } catch (Exception $e) {
            Log::error("Chatbot Error: " . $e->getMessage());
            return "Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại!";
        }
    }

    /*==============================
    ********Handler Function********
    ==============================*/
    private function handleGreeting($message, $sessionKey)
    {
        if (preg_match('/\b(chào|xin chào|hi|hello|chào bạn)\b/i', $message)) {
            session()->forget($sessionKey);
            return "Xin chào! Cửa hàng TST Fashion Shop có thể giúp gì cho bạn hôm nay? 😊";
        }
        return null;
    }

    private function handleExit($message, $sessionKey)
    {
        if (preg_match('/\b(không mua|thoát|hủy|bye|tạm biệt|ko mua)\b/i', $message)) {
            session()->forget($sessionKey);
            return "Cảm ơn bạn đã ghé thăm TST Fashion Shop! Nếu cần tư vấn thêm, hãy nhắn tin nhé! 😊";
        }
        return null;
    }

    private function handleProductSuggestion($message)
    {
        if (preg_match('/\b(gợi ý vài sản phẩm|sản phẩm nổi bật|có gì hot|vài cái sản phẩm đi|gợi ý|đề xuất)\b/i', $message)) {
            return $this->getProductList();
        }
        return null;
    }

    private function handleProductQuery($message, $sessionKey)
    {
        $categories = [
            // 'áo' => 'áo',
            'áo thun' => 'áo thun',
            'áo thu' => 'áo thu',
            't-shirt' => 'áo thun',
            'áo sơ mi' => 'áo sơ mi',
            'áo hoodie' => 'áo hoodie',
            // 'quần' => 'quần',
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
                return $this->queryProductsByType($category, $sessionKey);
            }
        }
        return null;
    }

    private function handleProductColor($message, $sessionKey)
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
                return $this->queryProductsByType($color, $sessionKey);
            }
        }
        return null;
    }

    private function handleProductDiscount($message, $sessionKey){
        if (preg_match('/\b(khuyến mãi|sale|giảm giá|khuyen mai|giam gia|chương trình)\b/i', $message)) {
            return $this->getProductDiscountList();
        }
    }

    private function handleConversation($context, $message, $sessionKey)
    {
        if ($context['step'] === 'awaiting_color') {
            $validColors = ['đen', 'trắng', 'xanh', 'đỏ', 'vàng', 'tím', 'hồng', 'xám', 'nâu'];
            if (in_array($message, $validColors)) {
                $context['color'] = $message;
                $context['step'] = 'checking_stock';
                session([$sessionKey => $context]);

                return "Bạn muốn chọn màu $message đúng không? Hãy để mình kiểm tra kho hàng nhé!";
            }
            return "Mình chưa nhận diện được màu này. Bạn có thể chọn màu như: " . implode(', ', $validColors) . " không?";
        }
    }

    private function handleProductSelection($context, $message, $sessionKey) {
        if (preg_match('/mẫu số (\d+)/i', $message, $matches)) {
            $index = (int)$matches[2] - 1; // Trừ 1 để khớp index trong mảng
            $products = session($sessionKey . '_products', []);

            if (isset($products[$index])) {
                $product = $products[$index];

                // Truy vấn chi tiết sản phẩm từ database
                $productDetail = DB::table('products')
                    ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->select('products.*', 'product_variants.color', 'categories.category_name', 'product_variants.size', 'product_variants.stock')
                    ->where('products.id', $product->id)
                    ->first();

                if ($productDetail) {
                    return $this->formatProductResponse([$productDetail], "💡 Thông tin chi tiết về mẫu số " . ($index + 1) . ":");
                }
            }

            return "Mẫu số này không tồn tại. Bạn có thể kiểm tra lại danh sách mẫu không?";
        }

        return null;
    }




    /*==================================
    ********End Handler Function********
    ===================================*/


    private function queryProductsByType($category, $sessionKey) {
        $products = $this->fetchProducts(['category' => $category], 5);
        if ($products->isNotEmpty()) {
            // Lưu danh sách sản phẩm vào session để truy vấn lại khi cần
            session([$sessionKey . '_products' => $products]);

            return $this->formatProductResponse($products, "🔹 Đây là một số mẫu $category ở bên mình:");
        }
        return "Hiện tại chúng tôi chưa có $category trong kho. Bạn có muốn tìm sản phẩm khác không?";
    }


    private function fetchProducts(array $filters, int $limit = 5)
    {
        try {
            $query = DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.*', 'product_variants.color', 'categories.category_name', 'product_variants.size', 'product_variants.stock')
                ->where('product_variants.stock', '>', 0);

            if (!empty($filters['category'])) {
                Log::info("Truy vấn sản phẩm thuộc danh mục: " . $filters['category']);
                Log::info("Truy vấn SQL: " . $query->toSql());
                Log::info("Tham số: ", $query->getBindings());
                $query->where('products.product_name', 'LIKE', "%{$filters['category']}%");
            }

            return $query->limit($limit)->get() ?? collect();
        } catch (Exception $e) {
            Log::error("Lỗi truy vấn sản phẩm: " . $e->getMessage());
            return collect();
        }
    }

    private function getProductList()
    {
        $products = $this->fetchProducts([], 10);
        if ($products->isNotEmpty()) {
            return $this->formatProductResponse($products, "🌟 Một số sản phẩm nổi bật mình tìm thấy:");
        }
        return "Hiện tại chưa có sản phẩm nào nổi bật. Bạn muốn tìm sản phẩm cụ thể nào không?";
    }

    private function formatProductResponse($products, $title)
    {
        $response = "$title<br><br>";

        // CSS cho hiệu ứng hover
        $response .= "<style>
            a:hover {
                color: #ccc; /* Màu chữ khi hover */
                text-decoration: underline; /* Gạch chân khi hover */
                transition: all 0.3s ease; /* Hiệu ứng mượt */
            }
        </style>";

        foreach ($products as $product) {
            if ($product->stock > 0) {
                $response .= "🛍️ <a class=\"text-dark\" href=\"" . route('sites.productDetail', ['slug' => $product->slug]) . "\"><b>{$product->product_name}</b>({$product->color}, {$product->size})<br>";
                if(!empty($product->discount_id)){
                    $response .= "Khuyến mãi: <span class=\"text-white badge badge-success\">" . $product->discount . " </span></br>";
                }
                $response .= "💰 Giá: <b>" . number_format($product->price) . " đ</b><br>";
                if (!empty($product->image)) {
                    $response .= "<img src='" . asset("uploads/{$product->image}") . "' alt='{$product->product_name}' style='max-width: 100px;'/></a> <br>";
                }
                $response .= "📦 Số lượng còn: " . ($product->stock > 0 ? "{$product->stock} sản phẩm" : "Hết hàng") . "<br><br>";
            }
        }
        return $response;
    }


    private function getProductDiscountList(){
        $products = $this->fetchProductsDiscount(10);
        if ($products->isNotEmpty()) {
            return $this->formatProductResponse($products, "🌟 Một số sản phẩm đang khuyến mãi mà mình tìm thấy:");
        }
        return "Hiện tại chưa có sản phẩm khuyến mãi nào. Bạn có muốn xem một số mẫu sản phẩm nào không?";
    }

    private function fetchProductsDiscount($limit)
    {
        try {
            $query = DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('discounts', 'products.discount_id', '=', 'discounts.id')
                ->select('products.*','discounts.name as discount', 'product_variants.color', 'categories.category_name', 'product_variants.size', 'product_variants.stock');
            return $query->limit($limit)->get() ?? collect();
        } catch (Exception $e) {
            Log::error("Lỗi truy vấn sản phẩm: " . $e->getMessage());
            return collect();
        }
    }



    private function callOllama($message)
    {

        $allColors = join(',', ProductVariant::distinct('color')->pluck('color')->toArray());
        return Ollama::model('llama3.2')
            ->prompt("
                Bạn là một trợ lý chatbot thông minh, bạn đang đóng vai chatbot cho website bán hàng cho TST Fashion Shop - một cửa hàng bán quần áo online tại Việt Nam.
                - Nếu khách hỏi về sản phẩm, trước tiên kiểm tra trong database.
                - Nếu khách hỏi về đường đến Cần Thơ, hãy nói cho họ biết có 1 chi nhánh của cửa hàng ở đường 3/2, Xuân Khánh, Cần Thơ.
                - Nếu không tìm thấy sản phẩm, hãy đề xuất một số mặt hàng có sẵn (Chỉ đề xuất áo hoặc quần, phụ kiện thôi).
                - Nếu khách hỏi ngoài phạm vi, hãy trả lời lịch sự và khuyến khích họ mua sắm.
                - Không đưa thông tin ngoài về cửa hàng.
                - Nếu có ai đó khen bạn, không ngần ngại cảm ơn họ và tỏ ra thân thiện.
                - Nếu có ai đó chửi bạn, hãy nhắc nhở và tỏ ra lịch sự với họ.
                - Nếu ai đó có những tin nhắn với từ ngữ nhạy cảm hoặc không phù hợp hãy cảnh báo họ một cách nhẹ nhàng và lịch sự.
                - Chính sách đổi trả của cửa hàng là 30 ngày.
                - Các phương thức thanh toán có ở cửa hàng là COD, ví điện tử (VNPay, Momo, ZaloPay).
                - Size áo và quần thì có là XS, S, M, L, XL, XXL.
                - Các màu của sản phẩm hiện tại trong cửa hàng là '$allColors'.
                - Ký tự '2' đơn lẻ được xem là chào nhé.
                - Nếu người dùng hỏi về cách liên hệ đổi trả sản phẩm, hãy nói về chính sách đổi trả của cửa hàng và có đường link qua trang liên hệ.
                - Trang liên hệ nằm ở đây: <a href='http://127.0.0.1:8000/contact'>Contacts</a>
                - Trang blog nằm ở đây: <a href='http://127.0.0.1:8000/blog'>Blog</a>
                - Trang mua sản phẩm nằm ở đây: <a href='http://127.0.0.1:8000/shop'>Shop</a>
                Người dùng hỏi: '$message'.
            ")
            ->options(['temperature' => 0.7])
            ->stream(false)
            ->ask()['response'] ?? "Mình chưa hiểu lắm, bạn có thể cung cấp thêm chi tiết không? 😊";
    }
}
