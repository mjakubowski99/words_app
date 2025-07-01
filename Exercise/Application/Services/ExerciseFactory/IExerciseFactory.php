<?php

namespace Exercise\Application\Services\ExerciseFactory;

use Shared\Exercise\IFlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\UserId;

interface IExerciseFactory
{
    /** @return IFlashcardExercise[] */
    public function make(ISessionFlashcardSummaries $summaries, UserId $user_id): array;
}