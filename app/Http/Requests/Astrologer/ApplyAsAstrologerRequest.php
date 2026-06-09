<?php

namespace App\Http\Requests\Astrologer;

use App\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplyAsAstrologerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'bio' => ['nullable', 'string', 'max:2000'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:100'],
            'price_per_minute' => ['required', 'integer', 'min:100'],
            'consultation_modes' => ['sometimes', 'array', 'min:1'],
            'consultation_modes.*' => [Rule::enum(ConsultationMode::class)],
            'expertise_ids' => ['required', 'array', 'min:1'],
            'expertise_ids.*' => ['integer', 'exists:expertises,id'],
            'language_ids' => ['required', 'array', 'min:1'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
        ];
    }
}
