<?php

namespace App\Domain\Shipping\Resources\ShippingAddress;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerShippingAddressResource extends JsonResource
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
            'firstname' => ucwords($this->firstname),
            'lastname' => ucwords($this->lastname),
            'phone_number' => $this->phone_number,
            'country' => ucwords($this->country->name),
            'state' => ucwords($this->state->name),
            'city' => ucwords($this->city->name),
            'address' => ucwords($this->address),
            'additional_note' => $this->additional_note,
            'status' => ucwords($this->status),
        ];
    }
}
