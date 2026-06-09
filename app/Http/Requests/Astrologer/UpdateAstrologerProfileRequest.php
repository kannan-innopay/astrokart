<?php

namespace App\Http\Requests\Astrologer;

use App\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAstrologerProfileRequest extends FormRequest
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
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'years_of_experience' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'price_per_minute' => ['sometimes', 'integer', 'min:100'],
            'consultation_modes' => ['sometimes', 'array', 'min:1'],
            'consultation_modes.*' => [Rule::enum(ConsultationMode::class)],
            'expertise_ids' => ['sometimes', 'array', 'min:1'],
            'expertise_ids.*' => ['integer', 'exists:expertises,id'],
            'language_ids' => ['sometimes', 'array', 'min:1'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
            'bank_account_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_account_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_ifsc_code' => ['sometimes', 'nullable', 'string', 'max:11'],
            'upi_id' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
