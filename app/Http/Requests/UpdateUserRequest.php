<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore(Auth::id()), 'max:255'],
            'password' => 'nullable|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/|max:255',
            'surname' => 'required|max:255',
            'birthDate' => 'required|before:today'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Password must be at least 8 characters long and contain at least one letter and one number.',
            'password.regex' => 'Password must be at least 8 characters long and contain at least one letter and one number.',
            'email.unique' => 'Email is already in use.',
            'email.email' => 'Email must be a valid email.'
        ];
    }
}
