<?php

namespace App\Domain\Shipping\Resources\PickupStation;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupStationOpeningHourResource extends JsonResource
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
            'day_of_week' => ucwords($this->day_of_week),
            'opening_hours' => self::parseTime($this->opens_at).' - '.self::parseTime($this->closes_at),
        ];
    }
}
