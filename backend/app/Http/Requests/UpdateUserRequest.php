<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->id === $this->route('user')->id;
    }

    public function rules()
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['sometimes', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,id'],
            'stores' => ['sometimes', 'array'],
            'stores.*' => ['exists:stores,id'],
            'two_factor_enabled' => ['sometimes', 'boolean'],
            'current_password' => ['required_with:password', 'current_password'],
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'The name must be a string',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'roles.*.exists' => 'One or more selected roles are invalid',
            'stores.*.exists' => 'One or more selected stores are invalid',
            'profile_picture.image' => 'The profile picture must be an image',
            'profile_picture.max' => 'The profile picture may not be greater than 2MB',
            'current_password.required_with' => 'Please provide your current password to change your password',
            'current_password.current_password' => 'The current password is incorrect',
        ];
    }

    protected function prepareForValidation()
    {
        // Remove empty values from arrays
        if ($this->has('roles')) {
            $this->merge([
                'roles' => array_filter($this->roles),
            ]);
        }

        if ($this->has('stores')) {
            $this->merge([
                'stores' => array_filter($this->stores),
            ]);
        }
    }
}