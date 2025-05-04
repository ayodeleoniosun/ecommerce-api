<?php

namespace App\Domain\Admin\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Admin\Actions\Category\CreateCategoryVariationOptions;
use App\Domain\Admin\Actions\Category\CreateCategoryVariations;
use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Requests\Category\CategoryVariationOptionRequest;
use App\Domain\Admin\Requests\Category\CategoryVariationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController
{
    public function __construct(
        private readonly CreateCategoryVariations $createCategoryVariations,
        private readonly CreateCategoryVariationOptions $createCategoryVariationOptions,
    ) {}

    public function storeCategoryVariations(CategoryVariationRequest $request): JsonResponse
    {
        if (! auth()->user()->hasDirectPermission('add-categories')) {
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

    public function storeCategoryVariationOptions(CategoryVariationOptionRequest $request): JsonResponse
    {
        $variationOptions = CreateCategoryVariationOptionDto::fromRequest($request);

        try {
            $variationOptions = $this->createCategoryVariationOptions->execute($variationOptions);

            return ApiResponse::success('Category variation options successfully added', $variationOptions,
                Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
