<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ];
    }
}
