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
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'email' => $this->getEmail(),
            'password' => Hash::make($this->getPassword()),
            'type' => $this->getType(),
        ];
    }

    public function getFirstname(): string
    {
        return $this->firstName;
    }

    public function getLastname(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
