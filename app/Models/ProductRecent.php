<?php

namespace App\Models;

class ProductRecent
{
    public $items = [];

    public function __construct()
    {
        $this->items = session('product_recent') ? session('product_recent') : [];
    }
    
    
    public function addToProductRecent($product)
    {
        $recentProducts = session()->get('product_recent', []);
    
        // Kiểm tra xem sản phẩm đã tồn tại chưa, nếu có thì xóa khỏi danh sách cũ
        foreach ($recentProducts as $key => $item) {
            if ($item->id_recent == $product->id) {
                unset($recentProducts[$key]);
            }
        }
        // Tạo thông tin sản phẩm mới
        $items = (object) [
            'key_recent' => $product->id,
            'id_recent' => $product->id,
            'name' => $product->product_name,
            'slug' => $product->slug,
            'image' => $product->image,
            'price' => $product->price
        ];
        // Thêm sản phẩm mới vào đầu mảng
        array_unshift($recentProducts, $items);
    
        // Tối đa hiện 8 cái
        if (count($recentProducts) > 8) {
            array_pop($recentProducts); // Loại bỏ sản phẩm cũ nhất
        }
        // Cập nhật lại session
        session()->put('product_recent', $recentProducts);
    }
    
    
    
    
    
    
    
    
    
    
}
