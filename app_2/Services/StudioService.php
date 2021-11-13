<?php

namespace App\Services;

use App\Dto\StudioDto;
use App\Models\Studio;
use App\Traits\HasUserTrait;

class StudioService
{
    use HasUserTrait;

    /**
     * @return Studio|\App\Queries\StudioQueryBuilder
     */
    public function fetch()
    {
        $studios = Studio::fetchAll();

        return $studios;
    }

    /**
     * @param StudioDto $dto
     * @return Studio
     */
    public function create(StudioDto $dto)
    {
        $studio = new Studio();
        $studio->title = $dto->getTitle();
        $studio->description = $dto->getDescription();
        $studio->save();

        return $studio;
    }

    /**
     * @param Studio $studio
     * @param StudioDto $studioDto
     * @return Studio
     */
    public function update(Studio $studio, StudioDto $studioDto)
    {
        if ($studioDto->getTitle()) {
            $studio->title = $studioDto->getTitle();
        }
        if ($studioDto->getDescription()) {
            $studio->description = $studioDto->getDescription();
        }

        $studio->save();

        return $studio;
    }
}
