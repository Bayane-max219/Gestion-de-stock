<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:cash,card,mixed'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'cash_amount' => ['required_if:payment_method,mixed', 'nullable', 'numeric', 'min:0'],
            'card_amount' => ['required_if:payment_method,mixed', 'nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'store_id.required' => 'A store must be selected',
            'store_id.exists' => 'The selected store does not exist',
            'items.required' => 'At least one item is required',
            'items.*.product_id.required' => 'A product must be selected for each item',
            'items.*.product_id.exists' => 'One or more selected products do not exist',
            'items.*.quantity.required' => 'Quantity is required for each item',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.unit_price.required' => 'Unit price is required for each item',
            'items.*.unit_price.min' => 'Unit price cannot be negative',
            'items.*.subtotal.required' => 'Subtotal is required for each item',
            'items.*.subtotal.min' => 'Subtotal cannot be negative',
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Invalid payment method selected',
            'paid_amount.required' => 'Paid amount is required',
            'paid_amount.min' => 'Paid amount cannot be negative',
            'cash_amount.required_if' => 'Cash amount is required for mixed payment',
            'card_amount.required_if' => 'Card amount is required for mixed payment'
        ];
    }
}