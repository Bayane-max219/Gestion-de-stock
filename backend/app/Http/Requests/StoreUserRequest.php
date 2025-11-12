<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // max 2MB
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
            'stores' => ['required', 'array'],
            'stores.*' => ['exists:stores,id'],
            'two_factor_enabled' => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required',
            'email.required' => 'The email is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'The password is required',
            'roles.required' => 'At least one role must be assigned',
            'roles.*.exists' => 'One or more selected roles are invalid',
            'stores.required' => 'At least one store must be assigned',
            'stores.*.exists' => 'One or more selected stores are invalid',
            'profile_picture.image' => 'The profile picture must be an image',
            'profile_picture.max' => 'The profile picture may not be greater than 2MB',
        ];
    }
}