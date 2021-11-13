<?php

namespace App\Services;

use App\Models\Player;

class PlayerService
{
    /**
     * @return mixed
     */
    public function list()
    {
        return Player::fetchAll();
    }
}
