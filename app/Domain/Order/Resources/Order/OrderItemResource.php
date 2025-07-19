<?php

namespace App\Domain\Order\Resources\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    use UtilitiesTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'product' => $this->cartItem->productItem->product->name,
            'variation' => $this->cartItem->productItem->variationOption->value,
            'quantity' => $this->cartItem->quantity,
            'unit_amount' => $this->cartItem->productItem->price,
            'total_amount' => $this->total_amount,
        ];
    }
}
