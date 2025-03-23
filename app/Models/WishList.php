<?php

namespace App\Models;

class Wishlist{
    public $items = [];

    public function __construct()
    {
        $this->items = session('wishlist') ? session('wishlist') : [];
    }


    public function addToWishlist($product, $productVariant = null)
    {
        if (!empty($this->items[$product->id])) {
            // $this->items[$product->id]->quantity += $quantity;
            return;
        } else {
            $items = [
                'id' => $product->id,
                'name' => $product->product_name,
                'image' => $product->image,
                'color' => $productVariant->color,
                'size' => $productVariant->size,
                'slug' => $product->slug
            ];
            $this->items[$product->id] = (object)$items;
        }

        session(['wishlist' => $this->items]);
    }




    public function removefromWishList($id)
    {
        if (!empty($this->items[$id])) {
            unset($this->items[$id]);
            session(['wishlist' => $this->items]);
        }
    }

}