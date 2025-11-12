<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CashRegister;

class CashRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'store_id' => 'required|exists:stores,id',
        ];

        // Additional rules based on the request type
        if ($this->is('*/open')) {
            $rules['opening_balance'] = 'required|numeric|min:0';
            $rules['notes'] = 'nullable|string|max:1000';
        } elseif ($this->is('*/close')) {
            $rules['actual_closing_balance'] = 'required|numeric|min:0';
            $rules['notes'] = 'nullable|string|max:1000';
            $rules['confirm_difference'] = 'required_if:large_difference,true|boolean';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'store_id.required' => 'Please select a store.',
            'store_id.exists' => 'The selected store does not exist.',
            'opening_balance.required' => 'Opening balance is required.',
            'opening_balance.numeric' => 'Opening balance must be a number.',
            'opening_balance.min' => 'Opening balance cannot be negative.',
            'actual_closing_balance.required' => 'Actual closing balance is required.',
            'actual_closing_balance.numeric' => 'Actual closing balance must be a number.',
            'actual_closing_balance.min' => 'Actual closing balance cannot be negative.',
            'confirm_difference.required_if' => 'Please confirm the large difference in balance.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->is('*/close')) {
            // Check if there's a large difference that needs confirmation
            $cashRegister = CashRegister::findOrFail($this->route('cash_register'));
            $expectedBalance = $cashRegister->current_balance;
            $actualBalance = $this->input('actual_closing_balance');
            $difference = abs($actualBalance - $expectedBalance);

            $this->merge([
                'large_difference' => $difference > config('app.max_cash_difference', 10),
            ]);
        }
    }
}