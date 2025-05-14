<?php

namespace App\Domain\Vendor\Products\Resource;

use App\Domain\Admin\Resources\Inventory\CategoryResource;
use App\Domain\Admin\Resources\User\VendorResource;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewProductResource extends JsonResource
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
            'name' => ucfirst($this->name),
            'description' => ucfirst($this->description),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'price_range' => ProductItem::getPriceRange($this->id),
            'variations' => ProductItemResource::collection($this->whenLoaded('items')),
            'images' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return $item->images->pluck('path');
                })->flatten();
            }),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
