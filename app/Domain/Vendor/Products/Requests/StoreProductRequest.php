<?php

namespace App\Domain\Vendor\Products\Requests;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreProductRequest extends FormRequest
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
            'category_id' => ['required', 'string', 'exists:categories,uuid'],
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
        $name = $this->input('name');

        if (empty($name)) {
            return;
        }

        $productExist = Product::where('name', $name)
            ->where('vendor_id', auth()->user()->id)
            ->exists();

        if ($productExist) {
            throw new HttpResponseException(
                ApiResponse::error('You have already added this product', Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
