<?php

namespace App\Domain\Order\Requests;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Domain\Order\Enums\CartOperationEnum;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddToCartRequest extends FormRequest
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
            'merged_product_item_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'max:3'],
            'type' => ['required', new Enum(CartOperationEnum::class)],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated($key, $default), [
            'user_id' => auth()->user()?->id,
        ]);
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
