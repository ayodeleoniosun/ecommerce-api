<?php

namespace App\Domain\Vendor\Products\Requests;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class StoreOrUpdateProductItemRequest extends FormRequest
{
    use OverrideDefaultValidationMethodTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'string', 'exists:products,uuid'],
            'product_item_id' => ['sometimes', 'string', 'exists:product_items,uuid'],
            'variation_option_id' => [
                'required',
                'string',
                Rule::exists('category_variation_options', 'uuid')->whereNull('deleted_at'),
            ],
            'merged_product_id' => ['required', 'integer'],
            'merged_product_item_id' => ['sometimes', 'integer'],
            'merged_variation_option_id' => ['required', 'integer'],
            'price' => ['required', 'integer'],
            'quantity' => ['required', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $productUUID = $this->input('product_id');
        $categoryVariationOptionUUID = $this->input('variation_option_id');

        if (empty($productUUID) || empty($categoryVariationOptionUUID)) {
            return;
        }

        $product = Product::where('uuid', $productUUID)
            ->where('vendor_id', auth()->user()->id)
            ->first();

        if (! $product) {
            throw new HttpResponseException(ApiResponse::error('Product does not exist', Response::HTTP_NOT_FOUND));
        }

        $categoryVariationOption = CategoryVariationOption::where('uuid', $categoryVariationOptionUUID)->first();

        $productItemUUID = $this->input('product_item_id');

        if (! empty($productItemUUID)) {
            $productItem = ProductItem::where('uuid', $productItemUUID)->first();

            $this->merge([
                'merged_product_item_id' => $productItem?->id,
            ]);
        }

        $this->merge([
            'merged_product_id' => $product->id,
            'merged_variation_option_id' => $categoryVariationOption?->id,
        ]);
    }
}
