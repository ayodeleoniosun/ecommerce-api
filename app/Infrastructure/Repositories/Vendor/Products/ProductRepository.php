<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Repositories\Inventory\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductDto $createOrUpdateProductDto): Product
    {
        if ($createOrUpdateProductDto->getProductId()) {
            $searchToUpdateBy = [
                'id' => $createOrUpdateProductDto->getProductId(),
            ];
        } else {
            $searchToUpdateBy = [
                'vendor_id' => $createOrUpdateProductDto->getVendorId(),
                'name' => $createOrUpdateProductDto->getName(),
            ];
        }

        $product = Product::updateOrCreate($searchToUpdateBy, $createOrUpdateProductDto->toArray());

        $product->load('vendor', 'category');

        return $product;
    }

    public function findExistingProduct(int $vendorId, string $name): ?Product
    {
        return Product::where('vendor_id', $vendorId)
            ->where('name', $name)
            ->first();
    }
}
