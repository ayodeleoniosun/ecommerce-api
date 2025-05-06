<?php

namespace App\Domain\Vendor\Products\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateProductRequest extends FormRequest
{
    use OverrideDefaultValidationMethodTrait;

    private ?int $requestCategoryId;

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
            'product_id' => ['sometimes', 'string', 'exists:products,uuid'],
            'category_id' => ['required', 'string', 'exists:categories,uuid'],
            'merged_product_id' => ['sometimes', 'integer'],
            'merged_category_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        return array_merge(parent::validated($key, $default), [
            'vendor_id' => auth()->user()->id,
        ]);
    }

    protected function prepareForValidation(): void
    {
        $categoryUUID = $this->input('category_id');

        if (empty($categoryUUID)) {
            return;
        }

        $category = Category::where('uuid', $categoryUUID)->first();

        $productUUID = $this->input('product_id');

        if (! empty($productUUID)) {
            $product = Product::where('uuid', $productUUID)
                ->where('vendor_id', auth()->user()->id)
                ->first();

            $this->merge([
                'merged_product_id' => $product?->id,
            ]);
        }

        $this->merge([
            'merged_category_id' => $category?->id,
        ]);
    }
}
