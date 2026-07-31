<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($this->route('product')->id)],
            'category_id' => [
                'sometimes',
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if (Category::where('parent_id', $value)->exists()) {
                        $fail('Products can only be added to leaf categories (categories with no subcategories).');
                    }
                },
            ],
            'sale_price'  => 'sometimes|required|numeric|min:0',
        ];
    }
}
