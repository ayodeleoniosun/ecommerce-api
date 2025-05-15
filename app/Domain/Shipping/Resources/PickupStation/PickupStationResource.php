<?php

namespace App\Domain\Shipping\Resources\PickupStation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupStationResource extends JsonResource
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
            'country' => ucwords($this->country->name),
            'state' => ucwords($this->state->name),
            'city' => ucwords($this->city->name),
            'name' => ucfirst($this->name),
            'address' => ucwords($this->address),
            'contact_phone_number' => $this->contact_phone_number,
            'contact_name' => ucwords($this->contact_name),
            'opening_hours' => PickupStationOpeningHourResource::collection($this->whenLoaded('openingHours')),
        ];
    }
}
