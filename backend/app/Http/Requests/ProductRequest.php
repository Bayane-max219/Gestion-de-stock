<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_alert_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];

        if ($this->isMethod('POST')) {
            $rules['sku'] = 'required|string|max:50|unique:products';
            $rules['barcode'] = 'nullable|string|max:50|unique:products';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['sku'] = ['required', 'string', 'max:50', Rule::unique('products')->ignore($this->product)];
            $rules['barcode'] = ['nullable', 'string', 'max:50', Rule::unique('products')->ignore($this->product)];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Product category is required',
            'category_id.exists' => 'Selected category does not exist',
            'supplier_id.required' => 'Product supplier is required',
            'supplier_id.exists' => 'Selected supplier does not exist',
            'name.required' => 'Product name is required',
            'name.max' => 'Product name cannot exceed 255 characters',
            'sku.required' => 'Product SKU is required',
            'sku.unique' => 'This SKU is already in use',
            'barcode.unique' => 'This barcode is already in use',
            'unit_price.required' => 'Unit price is required',
            'unit_price.min' => 'Unit price must be greater than or equal to 0',
            'selling_price.required' => 'Selling price is required',
            'selling_price.min' => 'Selling price must be greater than or equal to 0',
            'stock_alert_threshold.required' => 'Stock alert threshold is required',
            'stock_alert_threshold.min' => 'Stock alert threshold must be greater than or equal to 0',
        ];
    }
}