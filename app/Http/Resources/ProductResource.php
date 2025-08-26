<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'discount_id' => $this->discount_id,
            'name' => $this->product_name,
            'brand' => $this->brand,
            'sku' => $this->sku,
            'price' => $this->price,
            'image' => $this->image,
            'slug' => $this->slug,
            'description' => $this->description,
            'material' => $this->material,
            'category' => new CategoryResource($this->Category),
            'product-variant' => ProductVariantResource::collection($this->ProductVariants),
            'discount' => new DiscountResource($this->Discount),
            'star' => $this->comments()->avg('star') ?? 0,
            'comments_count' => $this->comments()->count() ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
