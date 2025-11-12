<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['nullable', 'string', 'in:pending,partial,paid'],
            'paid_amount' => ['required_with:payment_status', 'numeric', 'min:0'],
            'payment_method' => ['required_with:payment_status', 'string', 'in:cash,card,mixed'],
            'cash_amount' => ['required_if:payment_method,mixed', 'nullable', 'numeric', 'min:0'],
            'card_amount' => ['required_if:payment_method,mixed', 'nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:pending,completed,cancelled'],
            'notes' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'payment_status.in' => 'Invalid payment status',
            'paid_amount.required_with' => 'Paid amount is required when updating payment status',
            'paid_amount.min' => 'Paid amount cannot be negative',
            'payment_method.required_with' => 'Payment method is required when updating payment status',
            'payment_method.in' => 'Invalid payment method selected',
            'cash_amount.required_if' => 'Cash amount is required for mixed payment',
            'card_amount.required_if' => 'Card amount is required for mixed payment',
            'status.in' => 'Invalid sale status'
        ];
    }
}