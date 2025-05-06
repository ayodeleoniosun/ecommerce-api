<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Application\Shared\Traits\FileUploadTrait;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Vendor\Products\Dtos\UploadProductItemImageDto;
use App\Domain\Vendor\Products\Interfaces\ProductImageRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductImageResource;
use Illuminate\Http\UploadedFile;

class UploadProductItemImage
{
    use FileUploadTrait, UtilitiesTrait;

    public function __construct(
        private readonly ProductImageRepositoryInterface $productImageRepository,
    ) {}

    public function execute(UploadProductItemImageDto $uploadProductItemImageDto): ProductImageResource
    {
        $uuid = self::generateUUID();

        $path = $this->uploadImage($uploadProductItemImageDto->getImage(), $uuid);

        $uploadProductItemImageDto->setImagePath($path);

        $uploadProductItemImageDto->setUUID($uuid);

        $productImage = $this->productImageRepository->store($uploadProductItemImageDto);

        return new ProductImageResource($productImage);
    }

    private function uploadImage(UploadedFile $file, string $uuid): string
    {
        $filename = 'vendors/products/images/'.$uuid.'.jpg';

        return $this->uploadFile($file, $filename, 'public');
    }
}
