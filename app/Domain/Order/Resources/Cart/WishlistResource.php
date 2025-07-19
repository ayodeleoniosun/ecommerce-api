<?php

namespace App\Domain\Order\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'product_item_id' => $this->productItem->uuid,
            'product_name' => $this->productItem->product->name,
            'status' => $this->whenLoaded('status'),
            'unit_price' => number_format($this->productItem->price, 2),
            'image' => $this->productItem->firstImage->path ?? null,
            'attribute' => $this->productItem->variationOption->value,
        ];
    }
}
