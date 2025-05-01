<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Services\SessionFlashcardsService;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository  $next_session_flashcards_repository,
        private readonly IFlashcardSelector       $selector,
        private readonly SessionFlashcardsService $service,
//        private ExerciseTypePicker                $picker,
//        private IFlashcardExerciseFacade          $facade,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit());

//        foreach ($flashcards as $flashcard) {
//            if ($flashcard->getLastRating()->isGood()) {
//                $exercise_type = $this->picker->pick($command->getSessionType(), $command->getSessionId());
//
//                $count = $this->facade->getRequiredExerciseCount($exercise_type);
//
//                $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);
//
//                $additional_flashcards = $this->selector->select($next_session_flashcards, $count);
//
//                foreach ($additional_flashcards as $additional_flashcard) {
//                    $next_session_flashcards->addNextAdditional($additional_flashcard);
//                }
//
//                $this->next_session_flashcards_repository->save($next_session_flashcards);
//
//                $flashcards = array_merge([$flashcard], $additional_flashcards);
//
//                $exercise = $this->facade->makeExercise($this->buildSummaryObjects($ids, $flashcards), $command->getUserId(), $exercise_type);
//
//                return $exercise;
//            }
//        }

        $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);

        $this->next_session_flashcards_repository->save($next_session_flashcards);
    }
}
