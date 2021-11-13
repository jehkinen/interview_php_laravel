<?php

namespace App\Http\Requests;

use App\Dto\StudioDto;

class StudioRequest extends BaseFormRequest
{
    public function authorize()
    {
        $dto = new StudioDto(
            $this->input('title'),
            $this->input('description'),
        );

        $this->dto = $dto;

        return true;
    }

    public function rules()
    {
        if ($this->isCreateRequest()) {
            return [
                'title' => ['required', 'min:2', 'max:50'],
                'description' => ['required', 'min:10', 'max:500'],
            ];
        }

        return [
            'title' => ['min:2', 'max:50'],
            'description' => ['min:10', 'max:500'],
        ];
    }
}
