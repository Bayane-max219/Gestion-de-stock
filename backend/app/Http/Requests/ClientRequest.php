<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('clients')->ignore($this->client)],
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'tax_number' => ['nullable', 'string', 'max:50', Rule::unique('clients')->ignore($this->client)],
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms' => 'required|integer|min:0|max:180',
            'status' => 'required|in:active,inactive,blacklisted',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['email'] = ['sometimes', 'required', 'email', 'max:255', Rule::unique('clients')->ignore($this->client)];
            $rules['tax_number'] = ['nullable', 'string', 'max:50', Rule::unique('clients')->ignore($this->client)];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The client name is required.',
            'name.max' => 'The client name cannot exceed 255 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'The phone number is required.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'address.required' => 'The address is required.',
            'address.max' => 'The address cannot exceed 500 characters.',
            'city.required' => 'The city is required.',
            'city.max' => 'The city name cannot exceed 100 characters.',
            'tax_number.unique' => 'This tax number is already registered.',
            'credit_limit.required' => 'The credit limit is required.',
            'credit_limit.numeric' => 'The credit limit must be a number.',
            'credit_limit.min' => 'The credit limit cannot be negative.',
            'payment_terms.required' => 'The payment terms are required.',
            'payment_terms.integer' => 'The payment terms must be a whole number.',
            'payment_terms.min' => 'The payment terms cannot be negative.',
            'payment_terms.max' => 'The payment terms cannot exceed 180 days.',
            'status.required' => 'The client status is required.',
            'status.in' => 'Invalid client status.',
            'notes.max' => 'The notes cannot exceed 1000 characters.',
        ];
    }
}