<?php

declare(strict_types=1);

namespace Exercise\Application\Services\ExerciseFactory;

use Shared\Utils\ValueObjects\UserId;
use Shared\Exercise\IFlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;

interface IExerciseFactory
{
    /** @return IFlashcardExercise[] */
    public function make(ISessionFlashcardSummaries $summaries, UserId $user_id): array;
}
