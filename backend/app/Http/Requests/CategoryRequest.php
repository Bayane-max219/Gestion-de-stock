<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => 'nullable|string',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['parent_id'] = [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$this->category->id]),
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $parent = \App\Models\Category::find($value);
                        while ($parent) {
                            if ($parent->id === $this->category->id) {
                                $fail('Cannot create circular reference in category hierarchy.');
                                break;
                            }
                            $parent = $parent->parent;
                        }
                    }
                },
            ];
        } else {
            $rules['parent_id'] = 'nullable|exists:categories,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'parent_id.exists' => 'Selected parent category does not exist',
            'parent_id.not_in' => 'A category cannot be its own parent',
        ];
    }
}