<?php

namespace App\Http\Controllers\Api;

use App\Models\Player;
use App\Services\PlayerService;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\PlayerCollection;

class PlayerController extends Controller
{
    /** @var PlayerService */
    private $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * @param Player $player
     * @return PlayerResource
     */
    public function view(Player $player)
    {
        return new PlayerResource($player);
    }

    /**
     * @return PlayerCollection
     */
    public function index()
    {
        $players = $this->playerService->list();

        return new PlayerCollection($players);
    }
}
