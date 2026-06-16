<?php

namespace App\Http\Requests\Astrologer;

use App\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterAstrologerRequest extends FormRequest
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
            // Professional details
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:100'],
            'price_per_minute' => ['required', 'integer', 'min:5'],
            'consultation_modes' => ['sometimes', 'array', 'min:1'],
            'consultation_modes.*' => [Rule::enum(ConsultationMode::class)],
            'expertise_ids' => ['required', 'array', 'min:1'],
            'expertise_ids.*' => ['integer', 'exists:expertises,id'],
            'language_ids' => ['required', 'array', 'min:1'],
            'language_ids.*' => ['integer', 'exists:languages,id'],

            // Profile photos (at least one, multiple allowed)
            'photos' => ['required', 'array', 'min:1', 'max:6'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // KYC — entirely optional, but encouraged for a verified badge
            'aadhaar_number' => ['nullable', 'digits:12'],
            'aadhaar_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'pan_number' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'pan_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'certificate_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],

            // Bank details for settlements
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'bank_ifsc_code' => ['nullable', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(array_filter([
            'pan_number' => $this->pan_number ? strtoupper((string) $this->pan_number) : null,
            'bank_ifsc_code' => $this->bank_ifsc_code ? strtoupper((string) $this->bank_ifsc_code) : null,
        ]));
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Please upload at least one profile photo.',
            'pan_number.regex' => 'Please enter a valid PAN number (e.g. ABCDE1234F).',
            'bank_ifsc_code.regex' => 'Please enter a valid IFSC code (e.g. HDFC0001234).',
        ];
    }
}
