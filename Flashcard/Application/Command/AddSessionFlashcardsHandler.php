<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\DTO\SessionFlashcardSummary;
use Flashcard\Application\Services\ExerciseTypePicker;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Services\SessionFlashcardsService;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Shared\Enum\SessionExerciseType;
use Shared\Exercise\IExerciseFacade;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository $next_session_flashcards_repository,
        private readonly IFlashcardSelector $selector,
        private readonly SessionFlashcardsService $service,
        //private ExerciseTypePicker $picker,
        //private IExerciseFacade $facade,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit());

        $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);

        $this->next_session_flashcards_repository->save($next_session_flashcards);

//        $exercise_type = $this->picker->pick($next_session_flashcards->getExercisesType(), $next_session_flashcards->getSessionId());
//
//        if (!$next_session_flashcards->getExercisesType()) { // rating is good
//            $this->next_session_flashcards_repository->save($next_session_flashcards);
//            return;
//        }
//
//        $session_flashcard_ids = $this->next_session_flashcards_repository->saveGetId($next_session_flashcards);
//
//        $summary_objects = [];
//        foreach ($session_flashcard_ids as $session_flashcard_id) {
//            $summary_objects[] = new SessionFlashcardSummary();
//        }
//
//        //choose exercise based on session type
//        foreach ($summary_objects as $summary_object) {
//            $this->facade->makeUnscrambleWordExercise($summary_object);
//        }
    }
}
