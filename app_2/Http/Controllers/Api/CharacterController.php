<?php

namespace App\Http\Controllers\Api;

use App\Services\CharacterService;
use App\Http\Resources\CharacterCollection;

class CharacterController extends Controller
{
    /**
     * @var CharacterService
     */
    private $characterService;

    public function __construct(CharacterService $characterService)
    {
        $this->characterService = $characterService;
    }

    /**
     * Fetch list of all characters.
     * @return CharacterCollection
     */
    public function list()
    {
        return new CharacterCollection($this->characterService->list());
    }
}
