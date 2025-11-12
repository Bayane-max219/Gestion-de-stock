<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'from_store_id' => ['required', 'integer', 'exists:stores,id'],
            'to_store_id' => ['required', 'integer', 'exists:stores,id', 'different:from_store_id'],
            'quantity' => ['required', 'integer', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'A product must be selected',
            'product_id.exists' => 'The selected product does not exist',
            'from_store_id.required' => 'Source store must be selected',
            'from_store_id.exists' => 'The selected source store does not exist',
            'to_store_id.required' => 'Destination store must be selected',
            'to_store_id.exists' => 'The selected destination store does not exist',
            'to_store_id.different' => 'Source and destination stores must be different',
            'quantity.required' => 'Please specify the quantity to transfer',
            'quantity.min' => 'The quantity must be at least 1'
        ];
    }
}