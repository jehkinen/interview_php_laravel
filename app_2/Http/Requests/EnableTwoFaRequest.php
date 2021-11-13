<?php

namespace App\Http\Requests;

class EnableTwoFaRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'secret' =>  ['required', 'string', 'min:6'],
        ];
    }
}
