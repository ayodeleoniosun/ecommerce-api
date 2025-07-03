<?php

namespace App\Domain\Payment\Dtos;

class CustomerData
{
    public function __construct(
        private readonly string $email,
        private readonly ?string $name = null,
    ) {}

    public static function toArray(self $data): array
    {
        return [
            'email' => $data->email,
            'name' => $data->name ?? null,
        ];
    }
}
