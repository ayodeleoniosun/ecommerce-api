<?php

namespace App\Domain\Admin\Requests\Category;

use App\Application\Shared\Responses\OverrideDefaultValidationMethodTrait;
use App\Infrastructure\Models\CategoryVariation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'values' => ['required', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $categoryVariation = CategoryVariation::where('uuid', $this->input('variation_id'))->first();

        if ($categoryVariation === null) {
            return;
        }

        $this->requestVariationId = $categoryVariation->id;

        $this->merge([
            'request_variation_id' => $this->requestVariationId,
        ]);
    }
}
