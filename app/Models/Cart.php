<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Cart
{
    public $items = [];
    public $totalQty = 0;
    public $totalPrice = 0;
    public $cartQuantity = 0;

    public function __construct()
    {
        $this->items = session('cart') ? session('cart') : [];
        $this->totalQty = $this->getTotalQuantity();
        $this->totalPrice = $this->getTotalPrice();
        $this->cartQuantity = 1;
    }

    public function add($product, $quantity = 1, $productVariant = null)
    {
        $normalizedColor = str_replace(' ', '', $productVariant->color);
        $key = $product->id . '-' . $normalizedColor . '-' . $productVariant->size;
        if (!empty($this->items[$key])) {
            $this->items[$key]->quantity += $quantity;
        } else {
            $items = [
                'key' => $key,
                'id' => $product->id,
                'name' => $product->product_name,
                'slug' => $product->slug,
                'image' => $product->image,
                'price' => $product->price,
                'quantity' => $quantity,
                'product_variant_id' => $productVariant->id,
                'color' => $productVariant->color,
                'size' => $productVariant->size,
                'stock' => $productVariant->stock,
                'available_stock' => $productVariant->available_stock,
                'checked' => false,
            ];
            $this->items[$key] = (object)$items;
        }

        session(['cart' => $this->items]);
    }




    public function remove($key)
    {
        if (!empty($this->items[$key])) {
            unset($this->items[$key]);
            session(['cart' => $this->items]);
        }
    }

    private function getTotalQuantity()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->quantity;
        }
        return $total;
    }

    private function getTotalPrice()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->quantity * $item->price;
        }
        return $total;
    }



    // lưu cart vào db và megre cart với session
    public function saveToDatabase($customerId)
    {
        // Lấy giỏ hàng hiện có của user (nếu có)
        $existingCart = CartDatabase::where('customer_id', $customerId)->first();

        // Nếu không có giỏ hàng trong session, không cần làm gì
        if (empty($this->items)) {
            return $existingCart;
        }

        // Nếu user chưa có giỏ hàng, tạo mới
        if (!$existingCart) {
            try {
                $cart = CartDatabase::create([
                    'customer_id' => $customerId,
                    'cart_session_id' => session()->getId()
                ]);

                // Fixed: Use cart_id instead of id
                if (!$cart || !$cart->cart_id) {
                    throw new \Exception('Failed to create cart');
                }
            } catch (\Exception $e) {
                Log::error('Cart creation failed: ' . $e->getMessage());
                throw new \Exception('Unable to create cart for customer');
            }
        } else {
            $cart = $existingCart;
            // dd($cart);
        }

        // Fixed: Check cart_id instead of id
        if (!$cart || !$cart->cart_id) {
            throw new \Exception('Cart ID is missing after creation/retrieval');
        }

        // Merge các sản phẩm từ session vào giỏ hàng
        foreach ($this->items as $item) {
            // Fixed: Use cart_id instead of id
            $existingItem = CartDetailDatabase::where('cart_id', $cart->cart_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

                // dd($existingItem);

            if ($existingItem) {
                $existingItem->quantity += $item->quantity;
                $existingItem->save();
            } else {
                try {
                    CartDetailDatabase::create([
                        'cart_id' => $cart->cart_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'reserved_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Cart detail creation failed: ' . $e->getMessage());
                    throw new \Exception('Unable to add item to cart');
                }
            }
        }

        session()->forget('cart');
        return $cart;
    }

    public function getCartItemsOfCustomer($customerId)
    {
        // $customerId = Auth::guard('customer')->id();
        return CartDatabase::with('cartDetails', 'customer', 'cartDetails.product_variant')->where('customer_id', $customerId)->get();
    }
}
