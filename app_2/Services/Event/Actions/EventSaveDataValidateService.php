<?php

namespace App\Services\Event\Actions;

use App\Dto\EventDto;
use App\Models\Event;
use App\Models\Player;
use App\Models\Studio;
use App\Models\PlayerGroup;
use App\Models\EventTemplate;
use App\Traits\ValidationErrorTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventSaveDataValidateService
{
    use ValidationErrorTrait;

    protected $existsValidators;
    protected $models;

    /**
     * Setting up validation to check exists for some models.
     * @param EventDto $dto
     */
    public function setExistsValidators(EventDto $dto)
    {
        $this->existsValidators = [
            EventTemplate::class => [
                'id' => $dto->getEventTemplateId(),
                'message' => 'Event Template does not exists',
                'field' => 'event_template_id',
            ],
            Studio::class => [
                'id' => $dto->getStudioId(),
                'message' => 'Studio does not exists',
                'field' => 'studio_id',
            ],
        ];
    }

    /**
     * @param Event $event
     * @param EventDto $dto
     * @return Event
     */
    public function extractDto(Event $event, EventDto $dto): Event
    {

        $event->title = $dto->getTitle();
        $event->studio_id = $dto->getStudioId();
        $event->specific_data = $dto->getSpecificData();
        $event->event_template_id = $dto->getEventTemplateId();
        $event->start_at = $dto->getStartAt();
        $event->duration = $dto->getDuration();
        $event->color = $dto->getColor();
        $event->save();

        return $event;
    }

    /**
     * Some additional database checkings before saving event.
     *
     * @param EventDto $dto
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateDto(EventDto $dto)
    {
        $this->filterEntities($dto);
        $this->setExistsValidators($dto);

        foreach ($this->existsValidators as $modelClass => $item) {
            try {
                $this->models[$modelClass] = (new $modelClass)->findOrFail($item['id']);
            } catch (ModelNotFoundException $exception) {
                $this->throwClientError($item['field'], $item['message']);
            }
        }
    }

    /**
     * Filter non existed entities.
     *
     * @param EventDto $dto
     * @throws \ErrorException
     */
    public function filterEntities(EventDto $dto)
    {
        $filteredEntities = collect();

        if ($dto->getPlayerGroups()) {
            $filteredPlayerGroup = PlayerGroup::query()
                ->find($dto->getPlayerGroups())
                ->pluck('id')
                ->unique();

            $filteredPlayerGroup->each(function ($item, $key) use ($filteredEntities) {
                $filteredEntities->push([
                    'entity_id' => $item,
                    'entity_type' => PlayerGroup::shortClassName(),
                ]);
            });
        }

        if ($dto->getPlayers()) {
            $filteredPlayers = Player::query()
                ->find($dto->getPlayers())
                ->pluck('id')
                ->unique();

            $filteredPlayers->each(function ($item, $key) use ($filteredEntities) {
                $filteredEntities->push([
                    'entity_id' => $item,
                    'entity_type' => Player::shortClassName(),
                ]);
            });
        }
        $dto->setEntities($filteredEntities->all());
    }
}
