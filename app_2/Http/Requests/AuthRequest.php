<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'two_factor_code' => ['sometimes', 'max:6', 'nullable'],
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'Wrong login or password',
        ];
    }
}
