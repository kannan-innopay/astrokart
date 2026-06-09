<?php

namespace App\Http\Requests\User;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'gender' => ['sometimes', 'nullable', Rule::enum(Gender::class)],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'time_of_birth' => ['sometimes', 'nullable', 'string', 'max:10'],
            'place_of_birth' => ['sometimes', 'nullable', 'string', 'max:255'],
            'preferred_language' => ['sometimes', 'string', 'max:5'],
            'birth_latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'birth_longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
