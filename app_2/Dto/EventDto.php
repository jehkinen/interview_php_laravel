<?php

namespace App\Dto;

class EventDto
{
    private $title;
    private $eventTemplateId;
    private $startAt;
    private $duration;
    private $entities;
    private $color;
    private $privateNote;
    private $players;
    private $playerGroups;
    private $characters;
    private $eventSpecificPerson;
    private $publicNote;
    private $privateNotePlayerIds;
    private $studioId;
    private $specificData;

    /**
     * EventDto constructor.
     * @param $startAt
     * @param $duration
     * @param mixed $color
     * @param mixed $entities
     * @param mixed $eventTemplateId
     * @param mixed $studioId
     * @param null|mixed $privateNote
     * @param null|mixed $publicNote
     * @param mixed $privateNotePlayerIds
     * @param mixed $title
     * @param mixed $characters
     * @param mixed $playerGroups
     * @param mixed $players
     * @param null|mixed $eventSpecificPerson
     * @param null|mixed $specificData
     */
    public function __construct(
        $title,
        $eventTemplateId,
        $startAt,
        $duration,
        $color,
        $studioId,
        $privateNote = null,
        $publicNote = null,
        $privateNotePlayerIds = [],
        $characters = [],
        $playerGroups = [],
        $players = [],
        $eventSpecificPerson = null,
        $specificData = null
    ) {
        $this->title = $title;
        $this->eventTemplateId = $eventTemplateId;
        $this->startAt = $startAt;
        $this->duration = $duration;
        $this->color = $color;
        $this->studioId = $studioId;
        $this->privateNote = $privateNote;
        $this->publicNote = $publicNote;
        $this->privateNotePlayerIds = $privateNotePlayerIds;
        $this->characters = $characters;
        $this->playerGroups = $playerGroups;
        $this->players = $players;
        $this->eventSpecificPerson = $eventSpecificPerson;
        $this->specificData = $specificData;
    }

    /**
     * @return mixed|null
     */
    public function getSpecificData()
    {
        return $this->specificData;
    }

    /**
     * @return mixed|null
     */
    public function getEventSpecificPerson()
    {
        return $this->eventSpecificPerson;
    }

    /**
     * @return array|mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return array|mixed
     */
    public function getPlayerGroups()
    {
        return $this->playerGroups;
    }

    /**
     * @param array|mixed $players
     */
    public function setPlayers($players): void
    {
        $this->players = $players;
    }

    /**
     * @param array|mixed $playerGroups
     */
    public function setPlayerGroups($playerGroups): void
    {
        $this->playerGroups = $playerGroups;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array|mixed
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * @return mixed
     */
    public function getStudioId()
    {
        return $this->studioId;
    }

    /**
     * @return mixed
     */
    public function getPrivateNotePlayerIds()
    {
        return $this->privateNotePlayerIds;
    }

    /**
     * @return mixed
     */
    public function getEventTemplateId()
    {
        return $this->eventTemplateId;
    }

    /**
     * @return mixed|null
     */
    public function getPrivateNote()
    {
        return $this->privateNote;
    }

    /**
     * @return mixed|null
     */
    public function getPublicNote()
    {
        return $this->publicNote;
    }

    /**
     * @return mixed
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $entities
     */
    public function setEntities($entities): void
    {
        $this->entities = $entities;
    }
}
