<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAuthor();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'required|url',
            'screenshots' => 'nullable|string',
            'video_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
            'tags' => 'nullable|array',
        ];
    }
}
