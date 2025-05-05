<?php

namespace App\Domain\Admin\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'email' => $this->email,
        ];
    }
}
