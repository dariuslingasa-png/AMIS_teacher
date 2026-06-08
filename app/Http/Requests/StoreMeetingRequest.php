<?php

namespace App\Http\Requests;

use App\Enums\MeetingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMeetingRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:5', 'max:480'],
            'link' => ['nullable', 'url', 'max:255'],
            'agenda' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', new Enum(MeetingStatus::class)],
        ];
    }
}
