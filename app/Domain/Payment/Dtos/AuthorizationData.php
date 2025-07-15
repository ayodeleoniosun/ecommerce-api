<?php

namespace App\Domain\Payment\Dtos;

class AuthorizationData
{
    public function __construct(
        private readonly ?string $pin,
        private readonly ?string $otp,
        private readonly ?AvsAuthorizationData $avs,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            pin: $data['pin'] ?? null,
            otp: $data['otp'] ?? null,
            avs: $data['avs'] ?? null
        );
    }

    public static function toArray(self $data): array
    {
        return [
            'pin' => $data->pin,
            'otp' => $data->otp,
            'avs' => $data->avs,
        ];
    }

    public function getOtp(): ?string
    {
        return $this->otp;
    }

    public function getPin(): ?string
    {
        return $this->pin;
    }

    public function getAVS(): AvsAuthorizationData
    {
        return $this->avs;
    }
}
