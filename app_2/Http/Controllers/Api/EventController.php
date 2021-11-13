<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Traits\DestroyTrait;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Services\Event\Actions\EventListAction;
use App\Services\Event\Actions\EventSaveAction;

/**
 * Class EventController.
 */
class EventController extends Controller
{
    use DestroyTrait;

    protected $model = Event::class;

    /** @var EventListAction */
    protected $listAction;

    /** @var EventSaveAction */
    protected $saveAction;

    public function __construct(
        EventListAction $listAction,
        EventSaveAction $saveAction
    ) {
        $this->listAction = $listAction;
        $this->saveAction = $saveAction;
    }

    /**
     * Fetch Events list with studios, groups, event_types and assignments.
     */
    public function list()
    {
        return response()->json($this->listAction->run());
    }

    public function update(EventRequest $request, Event $event)
    {
        $event = $this->saveAction->run($event, $request->getDto());

        return new EventResource($event);
    }

    public function view(Event $event)
    {
        $event->load('note');

        return new EventResource($event);
    }

    /**
     * Create a new event.
     * @param EventRequest $request
     * @return EventResource
     */
    public function create(EventRequest $request)
    {
        $event = $this->saveAction->run(new Event(), $request->getDto());

        return new EventResource($event);
    }
}
