<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'A product must be selected',
            'product_id.exists' => 'The selected product does not exist',
            'store_id.required' => 'A store must be selected',
            'store_id.exists' => 'The selected store does not exist',
            'quantity.required' => 'Please specify the quantity to adjust',
            'quantity.not_in' => 'The quantity cannot be zero',
            'reason.required' => 'Please provide a reason for the adjustment'
        ];
    }
}