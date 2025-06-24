<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;


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
                'checked' => false
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



    // lưu cart vào db
    public function saveToDatabase($customerId)
    {
        // dd($customerId);
        if (empty($this->items)) return;
        // dd($this->items);

        $cart = CartDatabase::firstOrCreate([
            'customer_id' => $customerId,
            'cart_session_id' => session()->getId(),
        ]);
        // dd($cart);

        foreach ($this->items as $item) {
            $existing = CartDetailDatabase::where('cart_id', $cart->id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();


            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                CartDetailDatabase::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'reserved_at' => now(),
                ]);
            }
        }

        // Xoá cart session sau khi sync DB
        session()->forget('cart');
    }


    public function getCartItemsOfCustomer($customerId)
    {
        // $customerId = Auth::guard('customer')->id();
        return CartDatabase::with('cartDetails', 'customer', 'cartDetails.product_variant')->where('customer_id', $customerId)->get();
    }

}
