<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Models\Inventory\Category;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Repositories\Inventory\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    use UtilitiesTrait;

    public function index(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;
        $filter = $request->input('filter') ?? null;
        $sort = $request->input('sort') ?? null;
        $value = $request->input('value') ?? null;

        $products = Product::with(
            'category',
            'items',
            'firstItem',
            'firstItem.firstImage',
        )->when($request->isVendor, function ($query) {
            $query->where('vendor_id', auth()->user()->id);
        })->whereHas('items')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })->when($sort === 'date', function ($query) use ($value) {
                $query->whereHas('items', function ($q) use ($value) {
                    $q->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
                });
            })->when($sort === 'price', function ($query) use ($value) {
                $query->whereHas('items')
                    ->withMin('items as items_price_min', 'price')
                    ->orderBy('items_price_min', $value === 'asc' ? 'asc' : 'desc');
            })->when($filter === 'price' && is_array($value), function ($query) use ($value) {
                $query->whereHas('items', function ($q) use ($value) {
                    $q->whereBetween('price', [$value[0], $value[1]]);
                });
            })->when($filter === 'category', function ($query) use ($value) {
                $category = Category::where('uuid', $value)->first();
                $query->where('category_id', $category?->id);
            });

        if ($sort === 'date') {
            $products->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
        } else {
            $products->latest();
        }

        return $products->paginate(50);
    }

    public function storeOrUpdate(CreateOrUpdateProductDto $createOrUpdateProductDto): Product
    {
        $existingProduct = $this->findExistingProduct($createOrUpdateProductDto->getVendorId(),
            $createOrUpdateProductDto->getName());

        if ($existingProduct || $createOrUpdateProductDto->getProductId()) {
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

    public function view(Product $product): Product
    {
        return $product->load(['items', 'category', 'items.images', 'items.variationOption']);
    }
}
