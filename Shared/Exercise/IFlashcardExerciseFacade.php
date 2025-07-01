<?php

declare(strict_types=1);

namespace Shared\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\ISessionFlashcardSummary;

interface IFlashcardExerciseFacade
{
    /**
     * @return IFlashcardExercise[]
     */
    public function buildExercise(ISessionFlashcardSummaries $summaries, UserId $user_id, ExerciseType $type): array;
}
