<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'stock' => $this->stock,
            'in_cart' => $this->in_cart ?? false,
            'cart_id' => $this->cart_id ?? null,
            'in_borrowing' => $this->in_borrowing ?? false,
            'borrowing' => $this->borrowing ?? null,
        ];
    }
}
