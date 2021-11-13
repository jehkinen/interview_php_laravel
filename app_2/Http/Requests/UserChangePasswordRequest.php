<?php

namespace App\Http\Requests;

class UserChangePasswordRequest extends BaseFormRequest
{
    private const PASSWORD_REGEX = 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';

    public function rules()
    {
        return [
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed', self::PASSWORD_REGEX],
        ];
    }

    public function messages()
    {
        return [
            'new_password.regex' => 'New Password should contain lower and upper case characters and one digit at least, min length 8 characters',
        ];
    }
}
