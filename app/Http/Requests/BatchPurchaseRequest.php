<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchPurchaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code'                   => 'required|string|max:10|unique:batches,code',
            'provider_id'            => 'required|exists:providers,id',
            'storage_id'             => 'required|exists:storages,id',
            'products'               => 'required|array|min:1',
            'products.*.id'             => 'required|distinct|exists:products,id',
            'products.*.qty'            => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:1',
        ];
    }
}
