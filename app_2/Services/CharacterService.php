<?php

namespace App\Services;

use App\Models\Character;

class CharacterService
{
    public function list()
    {
        return Character::fetchAll();
    }
}
