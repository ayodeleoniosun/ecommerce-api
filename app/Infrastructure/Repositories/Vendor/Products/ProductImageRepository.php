<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\UploadProductItemImageDto;
use App\Domain\Vendor\Products\Interfaces\ProductImageRepositoryInterface;
use App\Infrastructure\Models\Inventory\ProductImage;
use App\Infrastructure\Repositories\Inventory\BaseRepository;

class ProductImageRepository extends BaseRepository implements ProductImageRepositoryInterface
{
    public function store(UploadProductItemImageDto $uploadProductItemImageDto): ProductImage
    {
        return ProductImage::create($uploadProductItemImageDto->toArray());
    }
}
