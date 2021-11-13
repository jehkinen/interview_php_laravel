<?php

namespace App\Http\Requests;

use LVR\Colour\Hex;
use App\Dto\EventDto;
use App\Models\Player;
use App\Models\PlayerGroup;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EventRequest extends BaseFormRequest
{
    public function validateResolved()
    {
        parent::validateResolved();

        $dto = new EventDto(
            $this->input('title'),
            $this->input('event_template_id'),
            $this->input('start_at'),
            $this->input('duration'),
            $this->input('color'),
            $this->input('studio_id'),
            $this->input('private_note'),
            $this->input('public_note'),
            $this->input('private_note_player_ids'),
            $this->input('characters'),
            $this->input('player_groups'),
            $this->input('players'),
            $this->input('event_specific_person'),
            $this->input('specific_data')
        );

        $this->dto = $dto;
    }

    public function rules()
    {
        return [
            'title' => ['sometimes', 'min:5', 'max:100'],
            'event_template_id' => 'required',
            'studio_id' => 'required',
            'start_at' => ['required'],
            'duration' => ['required', 'date_format:H:i'],
            'color' => ['required', new Hex()],

            'players' => ['array', 'sometimes'],
            'player_groups' => ['array', 'sometimes'],
            'private_note' => ['string', 'max:1000'],
            'public_note' => ['string', 'max:1000'],
            'private_note_player_ids' => ['array'],
            'event_specific_person' => ['string', 'max:100'],
            'specific_data' => [
                'array', 'sometimes',
            ],
        ];
    }
}
