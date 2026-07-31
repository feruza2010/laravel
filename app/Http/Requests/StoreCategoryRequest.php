<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:categories,name',
            'parent_id'   => [
                'nullable',
                'exists:categories,id',
                'required_without:provider_id',
                'prohibits:provider_id',
                function ($attribute, $value, $fail) {
                    if ($value && Product::where('category_id', $value)->exists()) {
                        $fail('Cannot add a subcategory to a category that already has products.');
                    }
                },
            ],
            'provider_id' => [
                'nullable',
                'exists:providers,id',
                'required_without:parent_id',
                'prohibits:parent_id',
            ],
        ];
    }
}
