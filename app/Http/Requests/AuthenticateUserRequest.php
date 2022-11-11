<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users',
            'password' => 'required',
            'remember' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Password must be at least 8 characters long and contain at least one letter and one number.',
            'email.email' => 'Email must be a valid email.'
        ];
    }
}
