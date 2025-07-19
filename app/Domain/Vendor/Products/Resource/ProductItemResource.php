<?php

namespace App\Domain\Vendor\Products\Resource;

use App\Domain\Vendor\Products\Enums\ProductStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductItemResource extends JsonResource
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
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'status' => $this->quantity > 0 ? ProductStatusEnum::IN_STOCK : ProductStatusEnum::OUT_OF_STOCK,
            'price' => number_format($this->price, 2),
            'attribute' => $this->whenLoaded('variationOption', function ($attribute) {
                return $attribute->value;
            }),
        ];
    }
}
