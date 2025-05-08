<?php

declare(strict_types=1);

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Application\DTO\ExerciseScore;
use Exercise\Domain\Models\Answer;
use Exercise\Domain\Models\AnswerAssessment;
use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\ExerciseEntry;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Flashcard\Domain\Models\Rating;
use Shared\Exceptions\UnauthorizedException;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\UserId;

abstract class AbstractExerciseAnswerHandler
{
    public function __construct(
        private IFlashcardFacade $facade,
    ) {}

    public function handle(ExerciseEntryId $id, UserId $user_id, Answer $answer): AnswerAssessment
    {
        $exercise = $this->resolveExercise($id);

        if (!$exercise->getUserId()->equals($user_id)) {
            throw new UnauthorizedException();
        }

        $assessment = $this->assessesAnswer($exercise, $answer);

        $this->save($exercise);

        return $assessment;
    }

    abstract protected function resolveExercise(ExerciseEntryId $id): Exercise;

    abstract protected function save(Exercise $exercise): void;

    protected function assessesAnswer(Exercise $exercise, Answer $answer): AnswerAssessment
    {
        $assessment = $exercise->assessAnswer($answer);

        if (!$exercise->getStatus()->isDone()) {
            return $assessment;
        }

        $exercise_scores = array_map(
            fn (ExerciseEntry $entry) => new ExerciseScore(
                $entry->getId()->getValue(),
                $entry->getScore()
            ),
            $exercise->getExerciseEntries()
        );

        $this->facade->updateRatings($exercise_scores);

        return $assessment;
    }
}
