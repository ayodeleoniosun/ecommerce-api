<?php

namespace App\Domain\User\Entities\User;

class User
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $email,
        private readonly string $password,
    ) {

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
}
