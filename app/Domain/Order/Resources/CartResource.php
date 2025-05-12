<?php

namespace App\Domain\Order\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'cart_quantity' => $this->quantity,
            'remaining_quantity' => $this->productItem->quantity,
            'unit_price' => number_format($this->productItem->price, 2),
            'total_price' => number_format($this->productItem->price * $this->quantity, 2),
            'image' => $this->productItem->firstImage->path ?? null,
            'attribute' => $this->productItem->variationOption->value,
        ];
    }
}
