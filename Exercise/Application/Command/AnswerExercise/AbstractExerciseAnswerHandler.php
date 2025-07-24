<?php

declare(strict_types=1);

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Domain\Models\Answer;
use Exercise\Domain\Models\Exercise;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Exercise\Domain\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Application\DTO\ExerciseScore;
use Exercise\Domain\Models\AnswerAssessment;
use Shared\Exceptions\UnauthorizedException;

abstract class AbstractExerciseAnswerHandler
{
    public function __construct(
        private IFlashcardFacade $facade,
    ) {}

    /** @return AnswerAssessment[] */
    public function handle(ExerciseId $exercise_id, UserId $user_id, array $answers): array
    {
        $exercise = $this->resolveExercise($exercise_id);

        if (!$exercise->getUserId()->equals($user_id)) {
            throw new UnauthorizedException();
        }

        $assessments = [];
        foreach ($answers as $answer) {
            $assessments[] = $this->assessesAnswer($exercise, $answer);
        }

        $this->save($exercise);

        return $assessments;
    }

    abstract protected function resolveExercise(ExerciseId $exercise_id): Exercise;

    abstract protected function save(Exercise $exercise): void;

    protected function assessesAnswer(Exercise $exercise, Answer $answer): AnswerAssessment
    {
        $assessment = $exercise->assessAnswer($answer);

        if (!$exercise->getStatus()->isDone()) {
            return $assessment;
        }

        $exercise_scores = array_map(
            fn (ExerciseEntry $entry) => new ExerciseScore(
                $entry->getId(),
                $entry->getScore()
            ),
            $exercise->getExerciseEntries()
        );

        $this->facade->updateRatings($exercise_scores);

        return $assessment;
    }
}
