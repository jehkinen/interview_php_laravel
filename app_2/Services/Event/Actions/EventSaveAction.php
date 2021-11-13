<?php

namespace App\Services\Event\Actions;

use App\Dto\EventDto;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Traits\ValidationErrorTrait;
use Illuminate\Validation\ValidationException;

class EventSaveAction
{
    use ValidationErrorTrait;

    /** @var EventSaveDataValidateService */
    private $eventDataValidationService;

    /** @var EventSaveRelatedEntitiesAction */
    private $saveRelatedEntitesService;

    public function __construct(
        EventSaveDataValidateService $eventDataValidationService,
        EventSaveRelatedEntitiesAction $eventSaveRelatedEntitiesAction
    ) {
        $this->eventDataValidationService = $eventDataValidationService;
        $this->saveRelatedEntitesService = $eventSaveRelatedEntitiesAction;
    }

    /**
     * @param EventDto $dto
     * @param Event $event
     * @throws \Illuminate\Validation\ValidationException
     * @return Event|false
     */
    public function run(Event $event, EventDto $dto)
    {
        try {
            DB::beginTransaction();

            $this->eventDataValidationService->validateDto($dto);
            $event = $this->eventDataValidationService->extractDto($event, $dto);
            $this->saveRelatedEntitesService->attachSpecificPerson($event, $dto);
            $this->saveRelatedEntitesService->attachNotes($event, $dto);
            $this->saveRelatedEntitesService->attachPrivateNotePlayers($event, $dto);
            $this->saveRelatedEntitesService->attachCharacters($event, $dto);
            $this->saveRelatedEntitesService->attachEntities($event, $dto);

            $event->save();

            DB::commit();

            $event->refresh();

            return $event;
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            app('sentry')->captureException($throwable);
            $this->throwClientError('event', 'Something went wrong during saving event, please try again');
        }

        return false;
    }
}
