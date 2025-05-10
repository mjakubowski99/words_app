<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Exercise\Domain\Exceptions\ExerciseAssessmentNotAllowedException;
use Exercise\Domain\Exceptions\ExerciseEntryNotFoundException;
use Exercise\Domain\Exceptions\ExerciseStatusTransitionException;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

abstract class Exercise
{
    /** @property ExerciseEntry[] $exercise_entries */
    public function __construct(
        private ExerciseId $id,
        private UserId $user_id,
        private array $exercise_entries,
        private ExerciseStatus $status,
        private ExerciseType $type,
    ) {}

    public function getId(): ExerciseId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getStatus(): ExerciseStatus
    {
        return $this->status;
    }

    public function getExerciseType(): ExerciseType
    {
        return $this->type;
    }

    public function skipExercise(): void
    {
        $this->setStatus(ExerciseStatus::SKIPPED);
    }

    public function markAsFinished(): void
    {
        $this->setStatus(ExerciseStatus::DONE);
    }

    public function setStatus(ExerciseStatus $status): void
    {
        if (in_array($this->status, [ExerciseStatus::NEW, ExerciseStatus::IN_PROGRESS], true)) {
            $this->status = $status;
        } else {
            throw new ExerciseStatusTransitionException($this->status, $status);
        }
    }

    public function assessAnswer(Answer $answer): AnswerAssessment
    {
        if (!$this->statusAllowsForAssessment()) {
            throw new ExerciseAssessmentNotAllowedException(
                'Exercise status does not allow for assessment. Current status: ' . $this->status->name
            );
        }

        $entry = $this->findOrFailExerciseEntry($answer);

        $assessment = $entry->getCorrectAnswer()->compare($answer);

        $entry->setLastUserAnswer($answer, $assessment);

        if ($this->status === ExerciseStatus::NEW) {
            $this->setStatus(ExerciseStatus::IN_PROGRESS);
        }
        if ($this->allAnswersCorrect()) {
            $this->setStatus(ExerciseStatus::DONE);
        }

        return $assessment;
    }

    /** @return ExerciseEntry[] */
    public function getExerciseEntries(): array
    {
        return $this->exercise_entries;
    }

    public function getUpdatedEntries(): array
    {
        return array_filter($this->exercise_entries, fn (ExerciseEntry $entry) => $entry->isUpdated());
    }

    public function allAnswersCorrect(): bool
    {
        /** @var ExerciseEntry $entry */
        foreach ($this->exercise_entries as $entry) {
            if (!$entry->isLastAnswerCorrect()) {
                return false;
            }
        }

        return true;
    }

    private function statusAllowsForAssessment(): bool
    {
        return in_array($this->status, [ExerciseStatus::NEW, ExerciseStatus::IN_PROGRESS], true);
    }

    private function findOrFailExerciseEntry(Answer $answer): ExerciseEntry
    {
        return $this->findExerciseEntry($answer)
            ?? throw new ExerciseEntryNotFoundException('Exercise entry with given id not found');
    }

    private function findExerciseEntry(Answer $answer): ?ExerciseEntry
    {
        foreach ($this->exercise_entries as $entry) {
            if ($entry->getId()->getValue() === $answer->getExerciseEntryId()->getValue()) {
                return $entry;
            }
        }

        return null;
    }
}
