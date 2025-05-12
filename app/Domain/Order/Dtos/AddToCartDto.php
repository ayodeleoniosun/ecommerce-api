<?php

namespace App\Domain\Order\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class AddToCartDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $productItemUUID,
        private readonly int $productItemId,
        private int $quantity,
        private ?int $userId = null,
        private ?int $cartId = null,
        private ?string $identifier = null,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            productItemUUID: $payload['product_item_id'],
            productItemId: $payload['merged_product_item_id'],
            quantity: $payload['quantity'],
            userId: $payload['user_id'] ?? null,
            identifier: $payload['identifier'] ?? null
        );
    }

    public function toCartArray(): array
    {
        return array_merge([
            'uuid' => self::generateUUID(),
        ], auth()->user()
            ? ['user_id' => $this->userId]
            : ['identifier' => $this->identifier],
        );
    }

    public function toCartItemArray(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'product_item_id' => $this->productItemId,
            'quantity' => $this->getQuantity(),
            'cart_id' => $this->cartId,
        ];
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getProductItemUUID(): string
    {
        return $this->productItemUUID;
    }

    public function getProductItemId(): int
    {
        return $this->productItemId;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
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
