<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Studio;
use App\Traits\DestroyTrait;
use App\Services\StudioService;
use App\Http\Requests\StudioRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StudioResource;
use App\Http\Resources\StudioCollection;

class StudioController extends Controller
{
    use DestroyTrait;

    protected $model = Studio::class;

    /** @var StudioService */
    protected $studioService;

    public function __construct(StudioService $playerGroupService)
    {
        if (Auth::check()) {
            /** @var User $currentUser */
            $currentUser = Auth::user();
            $playerGroupService->setUser($currentUser);
        }

        $this->studioService = $playerGroupService;
    }

    /**
     * @return StudioCollection
     */
    public function index()
    {
        $studios = $this->studioService->fetch();

        return new StudioCollection($studios);
    }

    /**
     * @param Studio $studio
     * @return StudioResource
     */
    public function view(Studio $studio)
    {
        return new StudioResource($studio);
    }

    /**
     * Creates new studio.
     * @param StudioRequest $request
     * @return StudioResource
     */
    public function create(StudioRequest $request)
    {
        $studio = $this->studioService->create($request->getDto());

        return new StudioResource($studio);
    }

    /**
     * Update a existed studio.
     * @param StudioRequest $request
     * @param Studio $studio
     * @return StudioResource
     */
    public function update(StudioRequest $request, Studio $studio)
    {
        $studio = $this->studioService->update($studio, $request->getDto());

        return new StudioResource($studio);
    }
}
