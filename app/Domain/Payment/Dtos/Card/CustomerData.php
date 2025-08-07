<?php

namespace App\Domain\Payment\Dtos\Card;

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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
