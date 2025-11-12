<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CashTransaction;

class CashTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required', 'string', 'in:' . implode(',', [
                CashTransaction::TYPE_SALE,
                CashTransaction::TYPE_PURCHASE,
                CashTransaction::TYPE_INCOME,
                CashTransaction::TYPE_EXPENSE,
            ])],
            'amount' => 'required|numeric|gt:0',
            'description' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'reference_type' => 'nullable|string|required_if:type,sale,purchase',
            'reference_id' => 'nullable|string|required_if:type,sale,purchase|exists:' . $this->getReferenceTable() . ',id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Transaction type is required.',
            'type.in' => 'Invalid transaction type.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.gt' => 'Amount must be greater than zero.',
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 255 characters.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method.',
            'reference_type.required_if' => 'Reference type is required for sales and purchases.',
            'reference_id.required_if' => 'Reference ID is required for sales and purchases.',
            'reference_id.exists' => 'The referenced document does not exist.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    protected function getReferenceTable()
    {
        return match($this->input('type')) {
            CashTransaction::TYPE_SALE => 'sales',
            CashTransaction::TYPE_PURCHASE => 'purchases',
            default => null,
        };
    }

    protected function prepareForValidation()
    {
        // Ensure amount is positive for validation (will be converted to negative for expenses in model)
        if ($this->has('amount')) {
            $this->merge([
                'amount' => abs($this->input('amount')),
            ]);
        }
    }
}