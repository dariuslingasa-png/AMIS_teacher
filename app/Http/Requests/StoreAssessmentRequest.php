<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:120'],
            'max_score' => ['required', 'integer', 'min:1', 'max:500'],
            'date' => ['required', 'date'],
        ];
    }
}
