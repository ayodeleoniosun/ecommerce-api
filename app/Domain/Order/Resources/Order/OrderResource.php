<?php

namespace App\Domain\Order\Resources\Order;

use App\Application\Shared\Enum\DeliveryTypeEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'currency' => $this->currency,
            'reference' => $this->reference,
            'amount' => $this->payments->first()->order_amount,
            'delivery_type' => $this->shipping->delivery_type,
            'delivery_address' => $this->when($this->shipping->delivery_type === DeliveryTypeEnum::DOOR_DELIVERY->value,
                $this->shipping->delivery_address),
            'pickup_station_name' => $this->when($this->shipping->delivery_type === DeliveryTypeEnum::PICKUP_STATION->value,
                $this->shipping->pickup_station_name),
            'estimated_delivery_start_date' => $this->parseDateOnly($this->shipping->estimated_delivery_start_date),
            'estimated_delivery_end_date' => $this->parseDateOnly($this->shipping->estimated_delivery_end_date),
            'status' => $this->status,
        ];
    }
}
