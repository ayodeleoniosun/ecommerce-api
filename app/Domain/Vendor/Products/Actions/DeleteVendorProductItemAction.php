<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Infrastructure\Models\Inventory\ProductItem;

class DeleteVendorProductItemAction
{
    public function __construct(
        private readonly ProductItemRepositoryInterface $productItemRepository,
    ) {}

    public function execute(string $productItemUUID): ?bool
    {
        $productItem = $this->productItemRepository->findByColumn(ProductItem::class, 'uuid', $productItemUUID);

        throw_if(! $productItem, ResourceNotFoundException::class, 'Product item not found');

        return $this->productItemRepository->delete($productItem);
    }
}
