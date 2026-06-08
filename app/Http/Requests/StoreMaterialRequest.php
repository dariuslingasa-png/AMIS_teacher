<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['nullable', 'file', 'max:204800', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif,webp,mp4,mov,avi,webm'],
            'external_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->file('file') && ! $this->filled('external_url')) {
                $validator->errors()->add('file', 'Upload a file or paste a Google Drive link.');
            }
        });
    }
}
