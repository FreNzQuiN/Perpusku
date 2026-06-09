<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'Buku harus dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak tersedia.',
        ];
    }

}
