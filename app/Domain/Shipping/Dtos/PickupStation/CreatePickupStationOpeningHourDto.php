<?php

namespace App\Domain\Shipping\Dtos\PickupStation;

use App\Application\Shared\Traits\UtilitiesTrait;

class CreatePickupStationOpeningHourDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $pickupStationUUID,
        private readonly string $dayOfWeek,
        private readonly string $opensAt,
        private readonly string $closesAt,
        private readonly int $pickupStationId,

    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            pickupStationUUID: $payload['pickup_station_id'],
            dayOfWeek: strtolower($payload['day_of_week']),
            opensAt: $payload['opens_at'],
            closesAt: $payload['closes_at'],
            pickupStationId: $payload['merged_pickup_station_id']
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'pickup_station_id' => $this->pickupStationId,
            'day_of_week' => $this->dayOfWeek,
            'opens_at' => $this->opensAt,
            'closes_at' => $this->closesAt,
        ];
    }

    public function getPickupStationId(): int
    {
        return $this->pickupStationId;
    }

    public function getPickupStationUUID(): string
    {
        return $this->pickupStationUUID;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function getOpensAt(): string
    {
        return $this->opensAt;
    }

    public function getClosesAt(): string
    {
        return $this->closesAt;
    }
}
