<?php

namespace App\Domain\Admin\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Admin\Actions\Category\CreateCategoryVariations;
use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Requests\Category\CategoryVariationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController
{
    public function __construct(
        private readonly CreateCategoryVariations $createCategoryVariations,
    ) {}

    public function storeVariations(CategoryVariationRequest $request): JsonResponse
    {
        if (! $request->user->hasDirectPermission('add categories')) {
            return ApiResponse::error('You are not allowed to create category variations', Response::HTTP_FORBIDDEN);
        }

        $variation = CreateCategoryVariationDto::fromRequest($request);

        try {
            $data = $this->createCategoryVariations->execute($variation);

            return ApiResponse::success('Category variations successfully added', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
