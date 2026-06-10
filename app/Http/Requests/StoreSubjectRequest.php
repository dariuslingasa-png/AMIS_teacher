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
            'name' => ['nullable', 'string', 'max:120'],
            'grade' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'in:male,female'],
            'mode' => ['required', 'in:Face-to-Face,Flexible Online Learning'],
            'shift' => ['nullable', 'in:1st Shift,2nd Shift'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['required', 'string', 'max:120'],
        ];
    }
}
