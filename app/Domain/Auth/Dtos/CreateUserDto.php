<?php

namespace App\Domain\Auth\Dtos;

use App\Application\Http\Auth\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;

class CreateUserDto
{
    public function __construct(
        private readonly string $firstname,
        private readonly string $lastname,
        private readonly string $email,
        private readonly string $password,
        private readonly string $type,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            firstname: $request->firstname,
            lastname: $request->lastname,
            email: $request->email,
            password: $request->password,
            type: $request->type
        );
    }

    public function toArray(): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'type' => $this->type,
        ];
    }
}
