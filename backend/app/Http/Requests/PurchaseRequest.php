<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Purchase;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'store_id' => 'required|exists:stores,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'expected_date' => 'required|date|after:purchase_date',
            'payment_due_date' => 'required|date|after_or_equal:purchase_date',
            'status' => ['required', 'in:' . implode(',', [
                Purchase::STATUS_PENDING,
                Purchase::STATUS_RECEIVED,
                Purchase::STATUS_PARTIALLY_RECEIVED,
                Purchase::STATUS_CANCELLED,
            ])],
            'payment_status' => ['required', 'in:' . implode(',', [
                Purchase::PAYMENT_STATUS_PENDING,
                Purchase::PAYMENT_STATUS_PAID,
                Purchase::PAYMENT_STATUS_PARTIALLY_PAID,
            ])],
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.received_quantity' => 'nullable|numeric|min:0|lte:items.*.quantity',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['store_id'] = 'sometimes|required|exists:stores,id';
            $rules['supplier_id'] = 'sometimes|required|exists:suppliers,id';
            $rules['items'] = 'sometimes|required|array|min:1';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'store_id.required' => 'Please select a store.',
            'store_id.exists' => 'The selected store does not exist.',
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'purchase_date.required' => 'Purchase date is required.',
            'purchase_date.date' => 'Invalid purchase date.',
            'expected_date.required' => 'Expected delivery date is required.',
            'expected_date.date' => 'Invalid expected delivery date.',
            'expected_date.after' => 'Expected delivery date must be after purchase date.',
            'payment_due_date.required' => 'Payment due date is required.',
            'payment_due_date.date' => 'Invalid payment due date.',
            'payment_due_date.after_or_equal' => 'Payment due date must be on or after purchase date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'discount.numeric' => 'Discount must be a number.',
            'discount.min' => 'Discount cannot be negative.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product is required for all items.',
            'items.*.product_id.exists' => 'One or more selected products do not exist.',
            'items.*.quantity.required' => 'Quantity is required for all items.',
            'items.*.quantity.numeric' => 'Quantity must be a number.',
            'items.*.quantity.min' => 'Quantity must be greater than zero.',
            'items.*.unit_price.required' => 'Unit price is required for all items.',
            'items.*.unit_price.numeric' => 'Unit price must be a number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.received_quantity.numeric' => 'Received quantity must be a number.',
            'items.*.received_quantity.min' => 'Received quantity cannot be negative.',
            'items.*.received_quantity.lte' => 'Received quantity cannot exceed ordered quantity.',
        ];
    }
}