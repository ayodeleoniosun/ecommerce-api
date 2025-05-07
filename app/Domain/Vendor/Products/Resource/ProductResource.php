<?php

namespace App\Domain\Vendor\Products\Resource;

use App\Domain\Admin\Resources\Inventory\CategoryResource;
use App\Domain\Admin\Resources\User\VendorResource;
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
            'id' => $this->uuid,
            'name' => ucfirst($this->name),
            'description' => ucfirst($this->description),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'items' => ProductItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
