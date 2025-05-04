<?php

declare(strict_types=1);

namespace Shared\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Flashcard\ISessionFlashcardSummary;

interface IFlashcardExerciseFacade
{
    public function getExerciseSummaryByFlashcard(int $session_flashcard_id): ?IExerciseSummary;

    /** @param ISessionFlashcardSummary[] $session_flashcard_summaries */
    public function makeExercise(array $session_flashcard_summaries, UserId $user_id, ExerciseType $type): void;

    public function getUnscrambleWordExercise(ExerciseId $id): IUnscrambleWordExerciseRead;
}
