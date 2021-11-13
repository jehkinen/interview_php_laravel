<?php

namespace App\Http\Controllers\Api;

use App\Models\EventTemplate;
use App\Services\EventTemplateService;
use App\Http\Resources\EventTemplateResource;
use App\Http\Resources\EventTemplateCollection;

class EventTemplateController extends Controller
{
    /** @var EventTemplateService */
    public $eventTemplateService;

    public function __construct(EventTemplateService $eventTemplateService)
    {
        $this->eventTemplateService = $eventTemplateService;
    }

    /**
     * Return list of event templates.
     *
     * @return EventTemplateCollection
     */
    public function list()
    {
        return new EventTemplateCollection($this->eventTemplateService->list());
    }

    /**
     * @param EventTemplate $eventTemplate
     * @return EventTemplateResource
     */
    public function view(EventTemplate $eventTemplate)
    {
        return new EventTemplateResource($eventTemplate);
    }
}
