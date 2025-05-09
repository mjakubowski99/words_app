<?php

declare(strict_types=1);

namespace Exercise\Application\Facades;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Exercise\IFlashcardExercise;
use Shared\Exercise\IFlashcardExerciseFacade;
use Shared\Flashcard\ISessionFlashcardSummary;
use Exercise\Application\Services\FlashcardExerciseFactory;

class FlashcardExerciseFacade implements IFlashcardExerciseFacade
{
    public function __construct(
        private FlashcardExerciseFactory $exercise_factory,
    ) {}

    /**
     * @param  ISessionFlashcardSummary[] $session_flashcard_summaries
     * @return IFlashcardExercise[]
     * */
    public function buildExercise(array $session_flashcard_summaries, UserId $user_id, ExerciseType $type): array
    {
        return $this->exercise_factory->makeExercise($session_flashcard_summaries, $user_id, $type);
    }
}
