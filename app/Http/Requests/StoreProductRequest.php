<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:products,name',
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if (Category::where('parent_id', $value)->exists()) {
                        $fail('Products can only be added to leaf categories (categories with no subcategories).');
                    }
                },
            ],
            'sale_price'  => 'required|numeric|min:0',
        ];
    }
}
