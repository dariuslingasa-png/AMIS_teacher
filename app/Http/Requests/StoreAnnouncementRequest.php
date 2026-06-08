<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:140'],
            'audience' => ['nullable', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'body' => ['required', 'string', 'max:1200'],
        ];
    }
}
