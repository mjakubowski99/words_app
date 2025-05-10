<?php

declare(strict_types=1);

namespace Shared\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\ISessionFlashcardSummary;

interface IFlashcardExerciseFacade
{
    /**
     * @param  ISessionFlashcardSummary[] $session_flashcard_summaries
     * @return IFlashcardExercise[]
     */
    public function buildExercise(array $session_flashcard_summaries, UserId $user_id, ExerciseType $type): array;
}
