<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IActiveSessionRepository;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Shared\Exercise\IExerciseScore;

class UpdateRatingsHandler
{
    public function __construct(
        private readonly IActiveSessionRepository $active_session_repository,
        private readonly IRepetitionAlgorithm     $repetition_algorithm,
    ) {}

    public function handle(array $exercise_scores): void
    {
        $ratings = $this->indexData($exercise_scores);

        $sessions = $this->active_session_repository->findByExerciseEntryIds(
            array_map(fn(IExerciseScore $rating) => $rating->getExerciseEntryId(), $ratings)
        );

        foreach ($sessions as $session) {
            foreach ($session->getSessionFlashcards() as $session_flashcard) {
                if (!$session_flashcard->hasExercise()) {
                    continue;
                }
                $session->rateByExerciseScore(
                    $session_flashcard->getSessionFlashcardId(),
                    $ratings[$session_flashcard->getExerciseEntryId()]->getScore(),
                );
            }
            $this->active_session_repository->save($session);
            $this->repetition_algorithm->handle($session);
        }
    }

    /** @return array<int,IExerciseScore> */
    private function indexData(array $exercise_scores): array
    {
        $indexed_data = [];
        foreach ($exercise_scores as $exercise_score) {
            $indexed_data[$exercise_score->getExerciseEntryId()->getValue()] = $exercise_score;
        }
        return $indexed_data;
    }
}
