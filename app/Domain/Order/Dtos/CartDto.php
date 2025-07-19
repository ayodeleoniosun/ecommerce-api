<?php

namespace App\Domain\Order\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CartDto
{
    use UtilitiesTrait;

    public function __construct(
        private string $productItemUUID,
        private readonly int $productItemId,
        private readonly string $currency,
        private int $quantity,
        private string $type,
        private ?int $userId = null,
        private ?int $cartId = null,
        private ?string $identifier = null,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            productItemUUID: $payload['product_item_id'],
            productItemId: $payload['merged_product_item_id'],
            currency: $payload['currency'],
            quantity: $payload['quantity'],
            type: $payload['type'],
            userId: $payload['user_id'] ?? null,
            identifier: $payload['identifier'] ?? null
        );
    }

    public function toCartArray(): array
    {
        return array_merge([
            'uuid' => self::generateUUID(),
        ], auth()->user()
            ? ['user_id' => $this->getUserId(), 'currency' => $this->getCurrency()]
            : ['identifier' => $this->identifier],
        );
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function toCartItemArray(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'product_item_id' => $this->getProductItemId(),
            'quantity' => $this->getQuantity(),
            'cart_id' => $this->getCartId(),
            'reserved_until' => now()->addMinutes(20),
        ];
    }

    public function getProductItemId(): int
    {
        return $this->productItemId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProductItemUUID(): string
    {
        return $this->productItemUUID;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setProductItemUUID(string $productItemUUID): void
    {
        $this->productItemUUID = $productItemUUID;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setCartId(int $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
