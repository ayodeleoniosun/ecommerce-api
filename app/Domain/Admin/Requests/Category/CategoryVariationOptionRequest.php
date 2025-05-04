<?php

namespace App\Domain\Admin\Requests\Category;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\CategoryVariation;
use App\Infrastructure\Models\CategoryVariationOption;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CategoryVariationOptionRequest extends FormRequest
{
    protected ?int $requestVariationId = null;

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
            'category_variation_id' => ['required', 'string', 'exists:category_variations,uuid'],
            'values' => ['required', 'array', 'max:5'],
            'values.*' => ['required', 'string', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'values.*.distinct' => 'Duplicate values are not allowed.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $categoryVariationOptions = $this->input('values');

        if (! is_array($categoryVariationOptions)) {
            return;
        }

        $categoryVariation = CategoryVariation::where('uuid', $this->input('category_variation_id'))->first();

        if (! $categoryVariation) {
            return;
        }

        $existingOptions = [];

        foreach ($categoryVariationOptions as $option) {
            $categoryVariationOptionExist = CategoryVariationOption::where('value', $option)
                ->where('variation_id', $categoryVariation->id)
                ->exists();

            if ($categoryVariationOptionExist) {
                $existingOptions[] = $option;
            }
        }

        if (! empty($existingOptions)) {
            $options = implode(', ', $existingOptions);

            throw new HttpResponseException(
                ApiResponse::error($options.' already added as options to the selected category variation',
                    Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        $this->requestVariationId = $categoryVariation->id;

        $this->merge([
            'request_variation_id' => $this->requestVariationId,
        ]);
    }
}
