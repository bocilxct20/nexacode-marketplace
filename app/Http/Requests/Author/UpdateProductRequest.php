<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');
        return auth()->check() && auth()->id() === $product->author_id;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|url',
            'screenshots' => 'nullable|string',
            'video_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
            'tags' => 'nullable|array',
        ];
    }
}
