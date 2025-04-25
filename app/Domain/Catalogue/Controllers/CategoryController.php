<?php

namespace App\Application\Http\Catalogue\Controller;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Catalogue\Catalogue\Category\CreateProductCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController
{
    public function __construct(private readonly CreateProductCategory $createProductCategory) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->createProductCategory->execute($request->name);

            return ApiResponse::success('Product category successfully created', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
