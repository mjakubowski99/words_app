<?php

declare(strict_types=1);

namespace Exercise\Application\Command;

use Exercise\Domain\Models\Answer;
use Flashcard\Domain\Models\Rating;
use Exercise\Domain\Models\Exercise;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Exercise\Domain\Models\ExerciseEntry;
use Exercise\Domain\Models\AnswerAssessment;
use Shared\Exceptions\UnauthorizedException;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Application\DTO\SessionFlashcardRating;

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

        $session_flashcard_ratings = array_map(
            fn (ExerciseEntry $entry) => new SessionFlashcardRating(
                $entry->getSessionFlashcardId()->getValue(),
                Rating::fromScore($entry->getScore())
            ),
            $exercise->getExerciseEntries()
        );

        $this->facade->updateRatings($session_flashcard_ratings);

        return $assessment;
    }
}
