<?php

namespace App\Models;

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
        $key = $product->id . '-' . $productVariant->color . '-' . $productVariant->size;
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

}
