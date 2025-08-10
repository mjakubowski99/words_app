<?php

declare(strict_types=1);

namespace Exercise\Application\Command\SkipExercise;

use Exercise\Domain\Models\Exercise;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Exercise\Domain\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Application\DTO\ExerciseScore;
use Shared\Exceptions\UnauthorizedException;

abstract class AbstractSkipExerciseHandler
{
    public function __construct(
        private IFlashcardFacade $facade,
    ) {}

    public function handle(ExerciseId $id, UserId $user_id): void
    {
        $exercise = $this->findExercise($id);

        if (!$exercise->getUserId()->equals($user_id)) {
            throw new UnauthorizedException();
        }

        $exercise->skipExercise();

        $this->saveExercise($exercise);

        $exercise_scores = array_map(
            fn (ExerciseEntry $entry) => new ExerciseScore(
                $entry->getId(),
                0.0
            ),
            $exercise->getExerciseEntries()
        );

        $this->facade->updateRatings($exercise_scores);
    }

    abstract protected function findExercise(ExerciseId $id): Exercise;

    abstract protected function saveExercise(Exercise $exercise): void;
}
