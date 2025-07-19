<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Actions\Cart\Wishlist\AddToWishlistAction;
use App\Domain\Order\Actions\Cart\Wishlist\GetWishlistItemsAction;
use App\Domain\Order\Actions\Cart\Wishlist\RemoveWishlistItemAction;
use App\Domain\Order\Dtos\WishlistDto;
use App\Domain\Order\Requests\AddToWishlistRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WishlistController
{
    public function __construct(
        private readonly GetWishlistItemsAction $wishlistItems,
        private readonly AddToWishlistAction $addToWishlist,
        private readonly RemoveWishlistItemAction $removeWishlistItem,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->wishlistItems->execute($request);

            return ApiResponse::success('Wishlist items retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(AddToWishlistRequest $request): JsonResponse
    {
        $wishlistDto = WishlistDto::fromRequest($request->validated());

        try {
            $data = $this->addToWishlist->execute($wishlistDto);

            return ApiResponse::success('Item successfully added to your wishlist', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function delete(string $wishlistItemUUID): JsonResponse
    {
        try {
            $this->removeWishlistItem->execute($wishlistItemUUID);

            return ApiResponse::success('Item successfully removed from your wishlist');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
