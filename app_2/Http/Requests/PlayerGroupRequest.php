<?php

namespace App\Http\Requests;

use App\Dto\PlayerGroupDto;

class PlayerGroupRequest extends BaseFormRequest
{
    public function validateResolved()
    {
        $this->dto = new PlayerGroupDto(
            $this->input('title'),
            $this->input('description'),
        );
    }

    public function rules()
    {
        return [
            'title' => ['required', 'min:5', 'max:50'],
            'description' => ['min:5', 'max:500'],
        ];
    }
}
