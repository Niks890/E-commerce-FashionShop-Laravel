<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vat'=>$this->vat,
            'status' => $this->status,
            'total_price' => $this->total,
            'createdate' => $this->created_at,
            'updatedate' => $this->updated_at,
            'staff' => [
                'id' => $this->staff->id,
                'name' => $this->staff->name,
            ],
            'provider' => [
                'id' => $this->provider->id,
                'name' => $this->provider->name,
            ],
            'detail' => $this->groupInventoryDetails(),
        ];
    }

    private function groupInventoryDetails()
    {
        $groupedDetails = [];

        foreach ($this->inventoryDetails as $detail) {
            $productId = $detail->product->id;

            // Nếu sản phẩm chưa có trong group, thêm mới
            if (!isset($groupedDetails[$productId])) {
                $groupedDetails[$productId] = [
                    'product' => [
                        'id' => $detail->product->id,
                        'name' => $detail->product->product_name,
                        'image' => $detail->product->image,
                        'brand' => $detail->product->brand,
                        'category' => [
                            'id' => $detail->product->category->id,
                            'name' => $detail->product->category->category_name,
                        ]
                    ],
                    'variants' => []
                ];
            }

            // Thêm variant vào sản phẩm - chỉ variant được nhập kho
            $groupedDetails[$productId]['variants'][] = [
                'id' => $detail->productVariant->id,
                'color' => $detail->productVariant->color,
                'color_code' => $this->getColorCode($detail->productVariant->color),
                'size' => $detail->productVariant->size,
                'price' => $detail->price,
                'quantity' => $detail->quantity,
                'stock' => $detail->productVariant->stock,
                'sizes' => $detail->productVariant->size . '-' . $detail->quantity . '-' . $detail->productVariant->color
            ];
        }

        return array_values($groupedDetails);
    }

    private function getColorCode($colorName)
    {
        $colorMap = [
            'Đỏ' => '#ff0000',
            'Xanh Nâu' => '#8FBC8F',
            'Xanh Dương' => '#0000ff',
            'Xanh Lá' => '#00ff00',
            'Vàng' => '#ffff00',
            'Tím' => '#800080',
            'Hồng' => '#ffc0cb',
            'Cam' => '#ffa500',
            'Đen' => '#000000',
            'Trắng' => '#ffffff',
            'Xám' => '#808080',
        ];

        return $colorMap[$colorName] ?? '#cccccc';
    }
}
