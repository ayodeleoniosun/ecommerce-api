<?php

namespace App\Domain\Vendor\Products\Resource;

use App\Domain\Admin\Resources\Inventory\CategoryVariationOptionResource;
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
            'price' => number_format($this->price, 2),
            'image' => new ProductImageResource($this->whenLoaded('firstImage')),
            'attribute' => new CategoryVariationOptionResource($this->whenLoaded('variationOption')),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }
}
