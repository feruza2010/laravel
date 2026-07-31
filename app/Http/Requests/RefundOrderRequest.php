<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RefundOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items'                 => 'required|array|min:1',
            'items.*.order_item_id' => ['required', 'integer', Rule::exists('order_items', 'id')],
            'items.*.batch_item_id' => 'required|integer|exists:batch_items,id',
            'items.*.qty'           => 'required|integer|min:1',
        ];
    }
}
