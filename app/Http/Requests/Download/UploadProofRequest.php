<?php

namespace App\Http\Requests\Download;

use Illuminate\Foundation\Http\FormRequest;

class UploadProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return auth()->check() && auth()->id() === $order->buyer_id;
    }

    public function rules(): array
    {
        return [
            'payment_proof' => 'required|image|max:10240', // 10MB max
        ];
    }
}
