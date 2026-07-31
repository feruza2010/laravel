<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id'      => 'required|exists:clients,id',
            'products'       => 'required|array|min:1',
            'products.*.id'  => 'required|distinct|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
        ];
    }
}
