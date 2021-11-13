<?php

namespace App\Http\Controllers\Api;

use App\Models\PlayerGroup;
use App\Traits\DestroyTrait;
use App\Services\PlayerGroupService;
use App\Http\Requests\PlayerGroupRequest;
use App\Http\Resources\PlayerGroupResource;
use App\Http\Resources\PlayerGroupCollection;

class PlayerGroupController extends Controller
{
    use DestroyTrait;

    protected $model = PlayerGroup::class;

    /** @var PlayerGroupService */
    protected $playerGroupService;

    public function __construct(PlayerGroupService $playerGroupService)
    {
        $this->playerGroupService = $playerGroupService;
    }

    /**
     * @param PlayerGroup $playerGroup
     * @return PlayerGroupResource
     */
    public function view(PlayerGroup $playerGroup)
    {
        return new PlayerGroupResource($playerGroup);
    }

    /**
     * @return PlayerGroupCollection
     */
    public function list()
    {
        $playerGroups = $this->playerGroupService->list();

        return new PlayerGroupCollection($playerGroups);
    }

    /**
     * @param PlayerGroupRequest $request
     * @return PlayerGroupResource
     */
    public function create(PlayerGroupRequest $request)
    {
        $playerGroup = $this->playerGroupService->create($request->getDto());

        return new PlayerGroupResource($playerGroup);
    }

    /**
     * @param PlayerGroupRequest $request
     * @param PlayerGroup $playerGroup
     * @return PlayerGroupResource
     */
    public function update(PlayerGroupRequest $request, PlayerGroup $playerGroup)
    {
        $playerGroup = $this->playerGroupService->update($playerGroup, $request->getDto());

        return new PlayerGroupResource($playerGroup);
    }
}
