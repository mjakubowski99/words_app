<?php

namespace Exercise\Application\Command;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\ValueObjects\AnswerEntryId;

class AnswerExerciseHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
    ) {}

    public function handle(AnswerEntryId $id, string $user_answer): bool
    {
        $exercise = $this->repository->findByAnswerEntryId($id);

        $answer = UnscrambleWordAnswer::fromString($id, $user_answer);

        $assessment = $exercise->assessAnswer($answer);

        $this->repository->save($exercise);

        // resolve scores for session flashcards and map rating to scores

        return $assessment->isCorrect();
    }
}