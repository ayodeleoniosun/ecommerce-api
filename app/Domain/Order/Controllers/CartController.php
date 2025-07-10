<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Actions\AddToCartAction;
use App\Domain\Order\Actions\GetCartItemsAction;
use App\Domain\Order\Actions\RemoveCartItemAction;
use App\Domain\Order\Dtos\AddToCartDto;
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

    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        $cartDto = AddToCartDto::fromRequest($request->validated());

        try {
            $data = $this->addToCart->execute($cartDto);

            return ApiResponse::success('Item successfully added to cart', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function removeCartItem(string $cartItemUUID): JsonResponse
    {
        try {
            $this->removeCartItem->execute($cartItemUUID);

            return ApiResponse::success('Item successfully removed from cart');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
