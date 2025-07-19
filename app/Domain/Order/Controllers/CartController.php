<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Actions\Cart\AddToCartAction;
use App\Domain\Order\Actions\Cart\GetCartItemsAction;
use App\Domain\Order\Actions\Cart\RemoveCartItemAction;
use App\Domain\Order\Dtos\CartDto;
use App\Domain\Order\Requests\AddToCartRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController
{
    public function __construct(
        private readonly GetCartItemsAction $cartItems,
        private readonly AddToCartAction $addToCart,
        private readonly RemoveCartItemAction $removeCartItem,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->cartItems->execute($request);

            return ApiResponse::success('Cart items retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        $cartDto = CartDto::fromRequest($request->validated());

        try {
            $data = $this->addToCart->execute($cartDto);

            return ApiResponse::success('Item successfully added to your cart', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function delete(string $cartItemUUID): JsonResponse
    {
        try {
            $this->removeCartItem->execute($cartItemUUID);

            return ApiResponse::success('Item successfully removed from your cart');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
