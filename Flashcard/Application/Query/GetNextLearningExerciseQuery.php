<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Exercise\IFlashcardExerciseFacade;
use Flashcard\Application\ReadModels\LearningExercisesRead;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;

class GetNextLearningExerciseQuery
{
    public function __construct(
        private ISessionFlashcardReadRepository $repository,
        private IFlashcardExerciseFacade $exercise_facade,
    ) {}

    public function get(SessionId $id, int $limit): LearningExercisesRead
    {
        $next_session_flashcards = $this->repository->findUnratedById($id, $limit);

        $exercises = [];

        foreach ($next_session_flashcards->getSessionFlashcards() as $flashcard) {
            $summary = $this->exercise_facade->getExerciseSummaryByFlashcard($flashcard->getId()->getValue());

            if ($summary) {
                $exercises[] = $summary;
            }
        }

        $next_session_flashcards->removeFlashcardsBelongingToExercises($exercises);

        return new LearningExercisesRead(
            $next_session_flashcards,
            $exercises,
        );
    }
}
