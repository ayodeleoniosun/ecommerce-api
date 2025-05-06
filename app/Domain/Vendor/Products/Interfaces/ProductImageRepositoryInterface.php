<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\UploadProductItemImageDto;
use App\Infrastructure\Models\ProductImage;

interface ProductImageRepositoryInterface
{
    public function store(UploadProductItemImageDto $uploadProductItemImageDto): ProductImage;
}
