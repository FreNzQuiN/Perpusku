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

    public function messages(): array
    {
        return [
            'borrow_date.required' => 'Tanggal pinjam harus diisi.',
            'borrow_date.date' => 'Format tanggal pinjam tidak valid.',
            'borrow_date.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lalu.',
            'duration_days.required' => 'Lama peminjaman harus diisi.',
            'duration_days.integer' => 'Lama peminjaman harus berupa angka.',
            'duration_days.min' => 'Lama peminjaman minimal 1 hari.',
            'duration_days.max' => 'Lama peminjaman maksimal 3 hari.',
            'book_ids.required' => 'Pilih setidaknya satu buku untuk dipinjam.',
            'book_ids.array' => 'Daftar buku tidak valid.',
            'book_ids.min' => 'Pilih setidaknya satu buku untuk dipinjam.',
            'book_ids.max' => 'Maksimal 10 buku dalam satu transaksi.',
            'book_ids.*.exists' => 'Salah satu buku yang dipilih tidak tersedia.',
            'book_ids.*.distinct' => 'Tidak boleh ada buku yang sama dalam satu transaksi.',
        ];
    }

}
