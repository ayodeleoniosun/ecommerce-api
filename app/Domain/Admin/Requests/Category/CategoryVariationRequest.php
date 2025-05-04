<?php

namespace App\Domain\Admin\Requests\Category;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\CategoryVariation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CategoryVariationRequest extends FormRequest
{
    protected ?int $requestCategoryId = null;

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
            'variations' => ['required', 'array', 'max:5'],
            'variations.*' => ['required', 'string', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'variations.*.distinct' => 'Duplicate variations are not allowed.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $categoryVariations = $this->input('variations');

        if (! is_array($categoryVariations)) {
            return;
        }

        $category = Category::where('uuid', $this->input('category_id'))->first();

        if (! $category) {
            return;
        }

        $existingVariations = [];

        foreach ($categoryVariations as $variation) {
            $categoryVariationExist = CategoryVariation::where('name', $variation)
                ->where('category_id', $category->id)
                ->exists();

            if ($categoryVariationExist) {
                $existingVariations[] = $variation;
            }
        }

        if (! empty($existingVariations)) {
            $variations = implode(', ', $existingVariations);

            throw new HttpResponseException(
                ApiResponse::error($variations.' already added as variations to the selected category',
                    Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        $this->requestCategoryId = $category->id;

        $this->merge([
            'request_category_id' => $this->requestCategoryId,
        ]);
    }
}
