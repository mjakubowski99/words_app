<?php

namespace Exercise\Application\Services;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\ExerciseId;
use Exercise\Domain\ValueObjects\SessionFlashcardId;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\UserId;

class FlashcardExerciseService
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
    ) {}

    public function makeUnscrambleWordsExercise(UserId $user_id, ISessionFlashcardSummary $summary): ExerciseId
    {
        $exercise = UnscrambleWordsExercise::newExercise(
            $user_id,
            new SessionFlashcardId($summary->getSessionFlashcardId()),
            $summary->getBackWord(),
            $summary->getFrontContext(),
            $summary->getFrontWord(),
            $summary->getEmoji(),
        );

        return $this->repository->create($exercise);
    }
}