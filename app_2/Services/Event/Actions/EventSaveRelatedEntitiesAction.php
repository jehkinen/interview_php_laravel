<?php

namespace App\Services\Event\Actions;

use App\Dto\EventDto;
use App\Models\Event;
use App\Models\Player;
use App\Models\EventTemplate;
use App\Models\EventTypeSpecificPerson;

class EventSaveRelatedEntitiesAction
{
    /**
     * @param Event $event
     * @param EventDto $dto
     * @param false $updateAction
     */
    public function attachPrivateNotePlayers(
        Event $event,
        EventDto $dto
    ) {
        $playerRawIds = collect($dto->getPrivateNotePlayerIds())->unique()->all();
        $playerIds = Player::query()->find($playerRawIds)->pluck('id');

        /** If it's already created entity we need to detach players as first */
        if (!$event->wasRecentlyCreated) {
            $event->hasPlayerAccesses()->delete();
        }

        $privateNotePlayers = $playerIds->map(function ($item, $key) {
            return [
                'player_id' => $item,
            ];
        });

        if ($privateNotePlayers->isNotEmpty()) {
            $event->hasPlayerAccesses()->createMany($privateNotePlayers->all());
        }
    }

    /**
     * @param Event $event
     * @param EventDto $dto
     * @return Event
     */
    public function attachSpecificPerson(Event $event, EventDto $dto)
    {
        if ($dto->getEventSpecificPerson()) {
            /** @var EventTemplate $eventTemplate */
            $eventTemplate = EventTemplate::query()->find($dto->getEventTemplateId());

            $eventTypeSpecificPerson = EventTypeSpecificPerson::query()->firstOrCreate([
                'event_type_id' => $eventTemplate->event_type_id,
                'title' => $dto->getEventSpecificPerson(),
            ]);
            $event->event_type_specific_person_id = $eventTypeSpecificPerson->id;
            $event->save();
        }

        return $event;
    }

    /**
     * Save Players and PlayerGroups for event.
     * @param Event $event
     * @param EventDto $dto
     * @return Event
     */
    public function attachEntities(Event $event, EventDto $dto)
    {
        $event->hasEntities()->delete();

        if ($dto->getEntities()) {
            $event->hasEntities()->createMany($dto->getEntities());
        }

        return $event;
    }

    /**
     * Saving public or private notes for event.
     * @param Event $event
     * @param EventDto $dto
     * @return Event
     */
    public function attachNotes(Event $event, EventDto $dto)
    {
        $eventNote = $event->note()->firstOrCreate();

        if ($dto->getPublicNote()) {
            $eventNote->public_note = $dto->getPublicNote();
        }
        if ($dto->getPrivateNote()) {
            $eventNote->private_note = $dto->getPrivateNote();
        }
        $eventNote->save();

        return $event;
    }

    /**
     * @param $event
     * @param $dto
     * @return mixed
     */
    public function attachCharacters(Event $event, EventDto $dto)
    {
        $event->hasCharacters()->delete();

        if ($dto->getCharacters()) {
            $event->hasCharacters()->createMany($dto->getCharacters());
        }

        return $event;
    }
}
