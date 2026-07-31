<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchRefundRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.batch_item_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('batch_items', 'id'),
            ],
            'items.*.qty'           => ['required', 'integer', 'min:1'],
        ];
    }
}
