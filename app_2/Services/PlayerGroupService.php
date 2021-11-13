<?php

namespace App\Services;

use App\Dto\PlayerGroupDto;
use App\Models\PlayerGroup;
use App\Traits\HasUserTrait;

class PlayerGroupService
{
    use HasUserTrait;

    public function list()
    {
        return PlayerGroup::fetchAll();
    }

    /**
     * @param PlayerGroupDto $dto
     * @return PlayerGroup
     */
    public function create(PlayerGroupDto $dto)
    {
        $userGroup = new PlayerGroup();
        $userGroup->title = $dto->getTitle();
        $userGroup->description = $dto->getDescription();
        $userGroup->save();

        return $userGroup;
    }

    /**
     * @param PlayerGroup $playerGroup
     * @param PlayerGroupDto $dto
     * @return PlayerGroup
     */
    public function update(PlayerGroup $playerGroup, PlayerGroupDto $dto)
    {
        if ($dto->getTitle()) {
            $playerGroup->title = $dto->getTitle();
        }

        if ($dto->getDescription()) {
            $playerGroup->description = $dto->getDescription();
        }

        $playerGroup->save();

        return $playerGroup;
    }
}
