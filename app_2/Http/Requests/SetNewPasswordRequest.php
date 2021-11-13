<?php

namespace App\Http\Requests;

class SetNewPasswordRequest extends BaseFormRequest
{
    private const PASSWORD_REGEX = 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'password' => ['required', 'min:8', 'confirmed', self::PASSWORD_REGEX],
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password should contain lower and upper case characters and one digit at least, min length 8 characters',
        ];
    }
}
