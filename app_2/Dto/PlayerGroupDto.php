<?php

namespace App\Dto;

class PlayerGroupDto
{
    private $title;
    private $description;

    /**
     * @param $title
     * @param $description
     */
    public function __construct($title, $description = '')
    {
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
