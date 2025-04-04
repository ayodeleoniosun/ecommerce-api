<?php

namespace App\Domain\Auth\Dtos;

use Illuminate\Support\Facades\Hash;

class UserDto
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $email,
        private readonly string $password,
        private readonly string $type,
    ) {}

    public function toArray(): array
    {
        return [
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'type' => $this->type,
        ];
    }
}
