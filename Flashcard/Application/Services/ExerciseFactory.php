<?php

namespace Flashcard\Application\Services;

use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Enum\ExerciseType;
use Shared\Exercise\IExerciseFacade;
use Shared\Utils\ValueObjects\UserId;

class ExerciseFactory
{
    public function __construct(
        private IExerciseFacade $facade,
    ) {}

    public function make(SessionId $session_id, ExerciseType $type, UserId $user_id)
    {
        if ($type === ExerciseType::UNSCRAMBLE_WORDS) {
            $this->facade->makeUnscrambleWordExercise([], $user_id)
        }
    }


}