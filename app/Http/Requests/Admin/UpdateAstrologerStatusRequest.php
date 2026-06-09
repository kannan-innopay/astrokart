<?php

namespace App\Http\Requests\Admin;

use App\Enums\AstrologerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAstrologerStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(AstrologerStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
