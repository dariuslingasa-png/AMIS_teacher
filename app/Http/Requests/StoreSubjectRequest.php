<?php

namespace App\Http\Requests;

use App\Enums\LearningMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:30'],
            'grade' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:120'],
            'schedule' => ['nullable', 'string', 'max:120'],
            'room' => ['nullable', 'string', 'max:60'],
            'mode' => ['required', new Enum(LearningMode::class)],
        ];
    }
}
