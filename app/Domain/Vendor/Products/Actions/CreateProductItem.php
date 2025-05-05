<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Dtos\CreateProductItemDto;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductItemResource;

class CreateProductItem
{
    public function __construct(
        private readonly ProductItemRepositoryInterface $productItemRepository,
    ) {}

    public function execute(CreateProductItemDto $createProductItemDto): ProductItemResource
    {
        $productItem = $this->productItemRepository->store($createProductItemDto);

        return new ProductItemResource($productItem);
    }
}
