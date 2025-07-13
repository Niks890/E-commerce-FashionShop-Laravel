<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function cart()
    {
        return view('sites.cart.index');
    }

    public function checkout()
    {

        // Kiểm tra đăng nhập
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('user.login')->with('redirect_url', url()->current());
        }

        // Kiểm tra giỏ hàng
        if (!Session::has('cart') || count(Session::get('cart')) === 0) {
            return redirect()->route('sites.cart')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Kiểm tra stock trước khi hiển thị trang thanh toán
        $stockCheck = $this->checkStockBeforeCheckout();
        if (!$stockCheck['success']) {
            return redirect()->route('sites.cart')->with('error', $stockCheck['message']);
        }

        return view('sites.pages.checkout');
    }


    private function checkStockBeforeCheckout()
    {
        $cart = Session::get('cart');
        $variantIds = [];
        $errors = [];
        $hasError = false;

        // Lấy danh sách variant IDs
        foreach ($cart as $item) {
            $variantIds[] = $item->product_variant_id;
        }

        // Kiểm tra stock từ database
        $variants = ProductVariant::whereIn('id', $variantIds)->get();

        foreach ($cart as $key => $item) {
            $variant = $variants->firstWhere('id', $item->product_variant_id);

            if (!$variant) {
                $errors[] = "Sản phẩm {$item->name} không tồn tại";
                unset($cart[$key]);
                $hasError = true;
                continue;
            }

            if ($variant->available_stock < $item->available_stock) {
                if ($variant->available_stock == 0) {
                    $errors[] = "Sản phẩm {$item->name} đã hết hàng";
                    unset($cart[$key]);
                } else {
                    $errors[] = "Sản phẩm {$item->name} chỉ còn {$variant->available_stock} sản phẩm. Đã cập nhật số lượng.";
                    $item->available_stock = $variant->available_stock;
                }
                $hasError = true;
            }
        }

        // Cập nhật lại session nếu có thay đổi
        if ($hasError) {
            Session::put('cart', $cart);

            return [
                'success' => false,
                'message' => implode('<br>', $errors)
            ];
        }

        return ['success' => true];
    }


    // live stock js
    // public function checkStock(Request $request)
    // {
    //     $variantIds = $request->input('variant_ids', []);

    //     $stocks = ProductVariant::whereIn('id', $variantIds)
    //         ->pluck('available_stock', 'id');

    //     return response()->json($stocks);
    // }




    // Thêm vào giỏ hàng mặc định lấy theo id sản phẩm
    public function add(Cart $cart, Product $product, $quantity = 1)
    {
        // thêm trong chi tiết cái form add_to_cart
        $product = Product::with('Discount')->find($product->id);
        if ($product->discount_id != null) {
            $product->price = $product->price - ($product->price * $product->Discount->percent_discount);
        }

        if (request()->has('add_to_cart')) {
            $productVariant = ProductVariant::where('product_id', $product->id)
                ->where('size', request()->size)
                ->where('color', request()->color)
                ->first();

            if (!$productVariant) {
                return back()->with('error', 'Sản phẩm này hiện không có sẵn biến thể!');
            }


            $cartItems = Session::get('cart', []);
            $normalizedColor = str_replace(' ', '', $productVariant->color);

            $variantKey = $product->id . '-' . $normalizedColor . '-' . $productVariant->size;

            // Lấy số lượng đang có trong giỏ cho biến thể này (nếu có)
            $currentItem = isset($cartItems[$variantKey]) ? (array) $cartItems[$variantKey] : [];
            $currentQtyInCart = isset($currentItem['quantity']) ? $currentItem['quantity'] : 0;
            // Tổng số lượng muốn thêm (hiện tại + mới)
            $newQty = $currentQtyInCart + request()->quantity;

            // Kiểm tra tồn kho
            if ($newQty > $productVariant->available_stock) {
                return back()->with('error', "Tổng số lượng bạn chọn vượt quá tồn kho. Trong kho chỉ còn {$productVariant->available_stock}, sản phẩm hiện tại trong giỏ hàng đã có {$currentQtyInCart}.");
            }

            // Validate số lượng (bỏ max vì đã kiểm tra ở trên)
            request()->validate(
                [
                    'quantity' => 'required|numeric|min:1',
                ],
                [
                    'quantity.required' => 'Vui lòng nhập số lượng.',
                    'quantity.numeric' => 'Số lượng phải là số.',
                    'quantity.min' => 'Số lượng phải ít nhất là 1.',
                ]
            );

            // Thêm sản phẩm vào giỏ
            $cart->add($product, request()->quantity, $productVariant);

            return redirect()->route('sites.cart');
        }
        // thêm ở bên ngoài
        else {
            $product = Product::with('Discount')->find($product->id);
            if ($product->discount_id != null) {
                $product->price = $product->price - ($product->price * $product->Discount->percent_discount);
            }
            // dd(request()->all());
            // $productVariant = ProductVariant::where('product_id', $product->id)->first();
            $productVariant = ProductVariant::where('product_id', $product->id)
                ->where('available_stock', '>', 0)
                ->first();
            if (!$productVariant) {
                return back()->with('error', 'Sản phẩm này hiện không có sẵn biến thể!');
            }


            $cartItems = Session::get('cart', []);
            $normalizedColor = str_replace(' ', '', $productVariant->color);
            $variantKey = $product->id . '-' .  $normalizedColor . '-' . $productVariant->size;

            $currentItem = isset($cartItems[$variantKey]) ? (array)$cartItems[$variantKey] : [];
            $currentQty = isset($currentItem['quantity']) ? $currentItem['quantity'] : 0;

            $newQty = $currentQty + $quantity;
            if ($newQty > $productVariant->available_stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Vượt quá số lượng có trong kho. Tồn kho hiện tại: {$productVariant->available_stock}",
                    'stock' => $productVariant->available_stock,
                ]);
            }

            $cart->add($product, $quantity, $productVariant);
            $totalItems = collect(session()->get('cart', []))->sum('quantity');

            // Nếu gửi đi là request từ AJAX thì trả về JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'cart' => $cart,
                    'color' => $productVariant->color,
                    'size' => $productVariant->size,
                    'cart_count' => $totalItems,
                    'cart_product_count' => count(Session::get('cart'))
                ]);
            }
            return redirect()->route('sites.cart');
        }
    }

    public function update($id, $quantity = 1)
    {
        return redirect()->route('sites.cart');
    }

    public function remove($key, Cart $cart)
    {
        // dd($key);
        $cart->remove($key);
        return redirect()->route('sites.cart');
    }

    public function clear()
    {
        if (session()->has('cart')) {
            session()->forget('cart');
        }
        return redirect()->route('sites.cart');
    }

    public function updateCartSession(Request $request)
    {
        // dd($request->all());
        if (!Session::has('cart')) {
            return response()->json(['message' => 'Không có giỏ hàng!'], 400);
        }

        $cart = Session::get('cart');

        $normalizedColor = str_replace(' ', '', $request->color);
        $key = $request->product_id . '-' . $normalizedColor . '-' . $request->size;

        if (isset($cart[$key])) {
            $cart[$key]->quantity = (int) $request->quantity;
            Session::put('cart', $cart); // Cập nhật session
        }

        return response()->json(['message' => 'Giỏ hàng đã được cập nhật!']);
    }



    public function createPercentDiscountSession(Request $request)
    {
        try {
            $percentDiscount = $request->input('percent_discount', 0);
            $voucherId = $request->input('voucher_id');

            // Lưu vào session
            Session::put('percent_discount', $percentDiscount);

            if ($voucherId) {
                Session::put('voucher_id', $voucherId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Session updated successfully',
                'data' => [
                    'percent_discount' => $percentDiscount,
                    'voucher_id' => $voucherId
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCheckStatus(Request $request)
    {
        $cart = session('cart', []);
        // Kiểm tra danh sách key trả về
        if ($request->has('keys') && is_array($request->keys)) {
            foreach ($cart as $key => $item) {
                $cart[$key]->checked = in_array($key, $request->keys);
            }
            session(['cart' => $cart]);
        }
        return response()->json(['message' => 'Cập nhật thành công!', 'cart' => session('cart')]);
    }

    // kiểm tra stock khi click thanh toán
    public function checkStock(Request $request)
    {
        $variantIds = $request->input('variant_ids');
        $stocks = [];

        foreach ($variantIds as $variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $stocks[$variantId] = $variant->available_stock;
            }
        }

        return response()->json($stocks);
    }
}
