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
            'name' => $this->product_name,
            'brand' => $this->brand,
            'sku' => $this->sku,
            'price' => $this->price,
            'image' => $this->image,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => new CategoryResource($this->Category),
            'product-variant' => ProductVariantResource::collection($this->ProductVariants),
            'discount' => new DiscountResource($this->Discount),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
