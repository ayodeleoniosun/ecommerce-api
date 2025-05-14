<?php

namespace App\Domain\Vendor\Products\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductItemImageRequest extends FormRequest
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
            'product_item_id' => ['required', 'string', 'exists:product_items,uuid'],
            'merged_product_item_id' => ['sometimes', 'integer'],
            'image' => ['required', 'mimes:jpg,png,jpeg'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $productItemUUID = $this->input('product_item_id');

        if (empty($productItemUUID)) {
            return;
        }

        $productItem = ProductItem::where('uuid', $productItemUUID)->first();

        $this->merge([
            'merged_product_item_id' => $productItem?->id,
        ]);
    }
}
