<?php

namespace Tests\Feature\Vendor;

use App\Application\Shared\Enum\ProductEnum;
use App\Application\Shared\Enum\UserEnum;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\CategoryVariationOption;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\ProductImage;
use App\Infrastructure\Models\ProductItem;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
        'status' => UserEnum::ACTIVE->value,
    ]);

    $this->category = Category::factory()->create();

    $this->actingAs($this->user, 'sanctum');
});

describe('create or update vendor products', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'name' => 'product name 1',
        ];

        $response = $this->postJson('/api/vendors/products', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The category id field is required.');
    });

    it('should return an error if category uuid is invalid', function () {
        $payload = [
            'category_id' => 'invalid_category_uuid',
            'name' => 'product name',
            'description' => 'product description',
        ];

        $response = $this->postJson('/api/vendors/products', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected category id is invalid.');
    });

    it('should create a new product', function () {
        $payload = [
            'category_id' => $this->category->uuid,
            'name' => 'product name',
            'description' => 'product description',
        ];

        $response = $this->postJson('/api/vendors/products', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CREATED);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product successfully added')
            ->and($content->data->is_existing_product)->toBeFalse()
            ->and($content->data->product->name)->toBe(ucfirst($payload['name']))
            ->and($content->data->product->description)->toBe(ucfirst($payload['description']))
            ->and($content->data->product->category->id)->toBe($this->category->uuid);

    });

    it('should return an error if product does not exist while updating existing product', function () {
        $payload = [
            'product_id' => 'invalid_product_uuid',
            'name' => 'product name',
            'description' => 'product description',
        ];

        $response = $this->postJson('/api/vendors/products', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected product id is invalid.');
    });

    it('should update an existing product', function () {
        $payload = [
            'product_id' => Product::factory()->create([
                'vendor_id' => $this->user->id,
                'name' => 'product name',
            ])->uuid,
            'category_id' => $this->category->uuid,
            'name' => 'new product name',
            'description' => 'product description',
        ];

        $response = $this->postJson('/api/vendors/products', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product successfully updated')
            ->and($content->data->is_existing_product)->toBeTrue()
            ->and($content->data->product->name)->toBe(ucfirst($payload['name']))
            ->and($content->data->product->description)->toBe(ucfirst($payload['description']))
            ->and($content->data->product->category->id)->toBe($this->category->uuid);
    });
});

describe('create or update vendor product items', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'product_id' => Product::factory()->create([
                'vendor_id' => $this->user->id,
                'name' => 'product name',
            ])->uuid,
        ];

        $response = $this->postJson('/api/vendors/products/items', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The variation option id field is required.');
    });

    it('should return an error if product uuid is invalid', function () {
        $payload = [
            'product_id' => 'invalid_product_uuid',
            'variation_option_id' => CategoryVariationOption::factory()->create()->uuid,
        ];

        $response = $this->postJson('/api/vendors/products/items', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Product does not exist');
    });

    it('should return an error if variation option uuid is invalid', function () {
        $payload = [
            'product_id' => Product::factory()->create([
                'vendor_id' => $this->user->id,
                'name' => 'product name',
            ])->uuid,
            'variation_option_id' => 'invalid_option_id',
        ];

        $response = $this->postJson('/api/vendors/products/items', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected variation option id is invalid.');
    });

    it('should create a new product item', function () {
        $payload = [
            'product_id' => Product::factory()->create([
                'vendor_id' => $this->user->id,
                'name' => 'product name',
            ])->uuid,
            'variation_option_id' => CategoryVariationOption::factory()->create()->uuid,
            'price' => 3000,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/vendors/products/items', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CREATED);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product item successfully added')
            ->and($content->data->is_existing_product_item)->toBeFalse()
            ->and($content->data->product_item->quantity)->toBe($payload['quantity'])
            ->and($content->data->product_item->price)->toBe(number_format($payload['price'], 2))
            ->and($content->data->product_item->status)->toBe(ProductEnum::IN_STOCK->value);
    });

    it('should return an error if product item does not exist while updating product item', function () {
        $payload = [
            'product_id' => Product::factory()->create([
                'vendor_id' => $this->user->id,
                'name' => 'product name',
            ])->uuid,
            'product_item_id' => 'invalid_product_item_uuid',
            'variation_option_id' => CategoryVariationOption::factory()->create()->uuid,
            'price' => 3000,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/vendors/products/items', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected product item id is invalid.');
    });

    it('should update an existing product', function () {
        $product = Product::factory()->create([
            'vendor_id' => $this->user->id,
            'name' => 'product name',
        ]);

        $payload = [
            'product_id' => $product->uuid,
            'product_item_id' => ProductItem::factory()->create([
                'product_id' => $product->id,
            ])->uuid,
            'variation_option_id' => CategoryVariationOption::factory()->create()->uuid,
            'price' => 3000,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/vendors/products/items', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product item successfully updated')
            ->and($content->data->is_existing_product_item)->toBeTrue()
            ->and($content->data->product_item->quantity)->toBe($payload['quantity'])
            ->and($content->data->product_item->price)->toBe(number_format($payload['price'], 2))
            ->and($content->data->product_item->status)->toBe(ProductEnum::IN_STOCK->value);
    });
});

describe('upload product image', function () {
    it('should return an error if product item uuid is invalid', function () {
        $payload = [
            'product_item_id' => 'invalid_product_uuid',
            'image' => UploadedFile::fake()->image('product-item.jpg'),
        ];

        $response = $this->postJson('/api/vendors/products/images', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('The selected product item id is invalid.');
    });

    it('should upload a new product image', function () {
        $product = Product::factory()->create([
            'vendor_id' => $this->user->id,
            'name' => 'product name',
        ]);

        $payload = [
            'product_item_id' => ProductItem::factory()->create([
                'product_id' => $product->id,
            ])->uuid,
            'image' => UploadedFile::fake()->image('product-item.jpg'),
        ];

        $response = $this->postJson('/api/vendors/products/images', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CREATED);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product image successfully uploaded')
            ->and(str_contains($content->data->path, $content->data->id))->toBeTrue();
    });
});

describe('view product details', function () {
    it('should return an error if product is not found', function () {
        $productUUID = 'invalid_product_uuid';

        $response = $this->getJson('/api/vendors/products/'.$productUUID);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Product not found');
    });

    it('should return product details', function () {
        $product = Product::factory()->create([
            'vendor_id' => $this->user->id,
            'name' => 'product name',
        ]);

        $productItems = ProductItem::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_id' => $product->id, 'price' => 1000, 'quantity' => 10],
                ['product_id' => $product->id, 'price' => 2000, 'quantity' => 15],
                ['product_id' => $product->id, 'price' => 3000, 'quantity' => 20],
            ))->create([
                'variation_option_id' => CategoryVariationOption::factory()->create()->id,
            ]);

        ProductImage::factory()
            ->count(3)
            ->state(new Sequence(
                [
                    'product_item_id' => $productItems[0]->id,
                    'path' => 'vendors/products/images/1d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
                [
                    'product_item_id' => $productItems[0]->id,
                    'path' => 'vendors/products/images/2d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
                [
                    'product_item_id' => $productItems[1]->id,
                    'path' => 'vendors/products/images/3d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
            ))->create();

        $response = $this->getJson('/api/vendors/products/'.$product->uuid);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product successfully retrieved')
            ->and($content->data->name)->toBe(ucfirst($product->name))
            ->and($content->data->price_range->min)->toBe(1000)
            ->and($content->data->price_range->max)->toBe(3000)
            ->and(collect($content->data->variations)->every(fn ($item) => $item->status))
            ->toEqual(ProductEnum::IN_STOCK->value)
            ->and(count($content->data->variations))->toBe(3)
            ->and(count($content->data->images))->toBe(3);
    });
});

describe('view all products', function () {
    it('should return all products', function () {
        $products = Product::factory()
            ->count(3)
            ->state(new Sequence(
                ['vendor_id' => $this->user->id, 'name' => 'product name 1'],
                ['vendor_id' => $this->user->id, 'name' => 'product name 2'],
                ['vendor_id' => $this->user->id, 'name' => 'product name 3'],
            ))->create();

        $productItems = ProductItem::factory()
            ->count(5)
            ->state(new Sequence(
                ['product_id' => $products[0]->id, 'price' => 1000, 'quantity' => 10],
                ['product_id' => $products[0]->id, 'price' => 2000, 'quantity' => 15],
                ['product_id' => $products[1]->id, 'price' => 3000, 'quantity' => 20],
                ['product_id' => $products[1]->id, 'price' => 4000, 'quantity' => 25],
                ['product_id' => $products[2]->id, 'price' => 5000, 'quantity' => 40],
            ))->create([
                'variation_option_id' => CategoryVariationOption::factory()->create()->id,
            ]);

        ProductImage::factory()
            ->count(3)
            ->state(new Sequence(
                [
                    'product_item_id' => $productItems[0]->id,
                    'path' => 'vendors/products/images/1d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
                [
                    'product_item_id' => $productItems[0]->id,
                    'path' => 'vendors/products/images/2d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
                [
                    'product_item_id' => $productItems[1]->id,
                    'path' => 'vendors/products/images/3d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
            ))->create();

        $response = $this->getJson('/api/vendors/products');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Products successfully retrieved');
    });
});

describe('delete product image', function () {
    it('should return an error if product image is not foumd', function () {
        $invalidUUID = 'invalid_uuid';

        $response = $this->deleteJson('/api/vendors/products/images/'.$invalidUUID);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Product image not found');
    });

    it('should delete product image', function () {
        $product = Product::factory()->create([
            'vendor_id' => $this->user->id,
        ]);

        $productItem = ProductItem::factory()->create([
            'product_id' => $product->id,
        ]);

        $productImage = ProductImage::factory()->create([
            'product_item_id' => $productItem->id,
        ]);

        $response = $this->deleteJson('/api/vendors/products/images/'.$productImage->uuid);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product image successfully deleted');
    });
});

describe('delete product item', function () {
    it('should return an error if product item is not found', function () {
        $invalidUUID = 'invalid_uuid';

        $response = $this->deleteJson('/api/vendors/products/items/'.$invalidUUID);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Product item not found');
    });

    it('should delete product item', function () {
        $product = Product::factory()->create([
            'vendor_id' => $this->user->id,
        ]);

        $productItem = ProductItem::factory()->create([
            'product_id' => $product->id,
        ]);

        $response = $this->deleteJson('/api/vendors/products/items/'.$productItem->uuid);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product item successfully deleted');
    });
});

describe('delete product', function () {
    it('should return an error if product is not found', function () {
        $invalidUUID = 'invalid_uuid';

        $response = $this->deleteJson('/api/vendors/products/'.$invalidUUID);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect($content->success)->toBeFalse()
            ->and($content->message)->toBe('Product not found');
    });

    it('should delete product item', function () {
        $product = Product::factory()->create([
            'vendor_id' => $this->user->id,
        ]);

        $productItems = ProductItem::factory()
            ->count(3)
            ->state(new Sequence(
                ['product_id' => $product->id, 'price' => 1000, 'quantity' => 10],
                ['product_id' => $product->id, 'price' => 2000, 'quantity' => 15],
                ['product_id' => $product->id, 'price' => 3000, 'quantity' => 20],
            ))->create([
                'variation_option_id' => CategoryVariationOption::factory()->create()->id,
            ]);

        ProductImage::factory()
            ->count(3)
            ->state(new Sequence(
                [
                    'product_item_id' => $productItems[0]->id,
                    'path' => 'vendors/products/images/1d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
                [
                    'product_item_id' => $productItems[0]->id,
                    'path' => 'vendors/products/images/2d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
                [
                    'product_item_id' => $productItems[1]->id,
                    'path' => 'vendors/products/images/3d9acfeb09904f828e8d26e0deb11b73.jpg',
                ],
            ))->create();

        $response = $this->deleteJson('/api/vendors/products/'.$product->uuid);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);

        expect($content->success)->toBeTrue()
            ->and($content->message)->toBe('Product successfully deleted');
    });
});
