<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductResourceCollection;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Repositories\Inventory\BaseRepository;
use Illuminate\Http\Request;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    use UtilitiesTrait;

    public function index(Request $request): ProductResourceCollection
    {
        $search = $request->input('search') ?? null;
        $filter = $request->input('filter') ?? null;
        $filterValue = $request->input('value') ?? null;

        $products = Product::with(
            'vendor',
            'category',
            'items',
            'items.variationOption',
            'items.variationOption.variation',
        )->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->when($filter, function ($query) use ($filter, $filterValue) {
            $query->with([
                'items' => function ($query) use ($filter, $filterValue) {
                    $filterColumn = self::filterColumn();

                    $query->orderBy($filterColumn[$filter], $filterValue === 'asc' ? 'asc' : 'desc');
                },
            ]);

            if ($filter === 'category') {
                $category = Category::where('uuid', $filterValue)->first();

                $query->where('category_id', $category?->id);
            }
        });

        if ($filter === 'date') {
            $products->orderBy('created_at', $filterValue === 'asc' ? 'asc' : 'desc');
        } else {
            $products->latest();
        }

        $result = $products->paginate(50);

        return new ProductResourceCollection($result);
    }

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
