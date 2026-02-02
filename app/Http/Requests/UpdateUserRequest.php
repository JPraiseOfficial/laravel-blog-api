<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends RegisterUserRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:50'],

            'username' => [
                'sometimes', 'string', 'alpha_dash', 'min:3', 'max:20',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],

            'email' => [
                'sometimes', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],

            'bio' => ['sometimes', 'nullable', 'string', 'max:500'],

            'gender' => ['sometimes', 'string', 'in:male,female,others'],
        ];
    }
}
