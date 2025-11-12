<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'store_id' => 'required|exists:stores,id',
            'client_id' => 'required|exists:clients,id',
            'payment_method' => 'required|in:cash,card,transfer,check',
            'payment_status' => 'required|in:pending,paid,partially_paid',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Make some fields optional on update
            $rules['store_id'] = 'sometimes|required|exists:stores,id';
            $rules['client_id'] = 'sometimes|required|exists:clients,id';
            $rules['payment_method'] = 'sometimes|required|in:cash,card,transfer,check';
            $rules['payment_status'] = 'sometimes|required|in:pending,paid,partially_paid';
            $rules['items'] = 'sometimes|required|array|min:1';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'store_id.required' => 'Please select a store.',
            'store_id.exists' => 'The selected store does not exist.',
            'client_id.required' => 'Please select a client.',
            'client_id.exists' => 'The selected client does not exist.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'payment_status.required' => 'Please select a payment status.',
            'payment_status.in' => 'Invalid payment status selected.',
            'discount.numeric' => 'Discount must be a number.',
            'discount.min' => 'Discount cannot be negative.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product is required for all items.',
            'items.*.product_id.exists' => 'One or more selected products do not exist.',
            'items.*.quantity.required' => 'Quantity is required for all items.',
            'items.*.quantity.numeric' => 'Quantity must be a number.',
            'items.*.quantity.min' => 'Quantity must be greater than zero.',
            'items.*.unit_price.numeric' => 'Unit price must be a number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
        ];
    }
}