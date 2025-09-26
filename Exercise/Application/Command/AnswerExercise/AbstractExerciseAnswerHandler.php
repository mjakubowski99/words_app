<?php

declare(strict_types=1);

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Application\DTO\ExerciseScore;
use Exercise\Domain\Models\Answer\Answer;
use Exercise\Domain\Models\AnswerAssessment;
use Exercise\Domain\Models\Exercise\Exercise;
use Exercise\Domain\Models\ExerciseEntry\ExerciseEntry;
use Shared\Exceptions\UnauthorizedException;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

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
