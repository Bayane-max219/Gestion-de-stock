<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable', 
                'email', 
                'max:255',
                Rule::unique('suppliers', 'email')->ignore($this->route('supplier'))
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'tax_number' => [
                'nullable', 
                'string', 
                'max:50',
                Rule::unique('suppliers', 'tax_number')->ignore($this->route('supplier'))
            ],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The supplier name is required',
            'name.max' => 'The supplier name cannot exceed 255 characters',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered to another supplier',
            'phone.max' => 'The phone number cannot exceed 20 characters',
            'tax_number.max' => 'The tax number cannot exceed 50 characters',
            'tax_number.unique' => 'This tax number is already registered to another supplier',
            'products.*.exists' => 'One or more selected products do not exist'
        ];
    }
}