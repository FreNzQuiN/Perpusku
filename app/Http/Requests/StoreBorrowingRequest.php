<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'borrow_date' => 'required|date|after_or_equal:today',
            'duration_days' => 'required|integer|min:1|max:3',
            'book_ids' => 'required|array|min:1|max:10',
            'book_ids.*' => 'exists:books,id|distinct',
        ];
    }

}
