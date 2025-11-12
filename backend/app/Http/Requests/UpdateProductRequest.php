<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules()
    {
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($product->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'unit' => ['required', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'is_active' => ['boolean'],
            'stores' => ['required', 'array'],
            'stores.*' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'sku.required' => 'The SKU is required.',
            'sku.unique' => 'This SKU is already in use.',
            'barcode.unique' => 'This barcode is already in use.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier is invalid.',
            'purchase_price.required' => 'The purchase price is required.',
            'purchase_price.min' => 'The purchase price must be greater than or equal to 0.',
            'selling_price.required' => 'The selling price is required.',
            'selling_price.min' => 'The selling price must be greater than or equal to 0.',
            'quantity.required' => 'The quantity is required.',
            'quantity.min' => 'The quantity cannot be negative.',
            'low_stock_threshold.required' => 'The low stock threshold is required.',
            'tax_rate.max' => 'The tax rate cannot exceed 100%.',
            'unit.required' => 'The unit of measurement is required.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image size cannot exceed 2MB.',
            'stores.required' => 'Please specify quantities for at least one store.',
        ];
    }
}