<?php

namespace Exercise\Domain\Models;

use Exercise\Domain\ValueObjects\ExerciseId;
use Shared\Enum\ExerciseType;

abstract class Exercise
{
    public function __construct(
        private ExerciseId $id,
        private array $answer_entries,
        private ExerciseStatus $status,
        private ExerciseType $type,
    ) {}

    public function getId(): ExerciseId
    {
        return $this->id;
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

    public function setStatus(ExerciseStatus $status): void
    {
        if (in_array($this->status, [ExerciseStatus::NEW, ExerciseStatus::IN_PROGRESS], true)) {
            $this->status = $status;
        } else {
            throw new \UnexpectedValueException("Status {$status->value} cannot be changed");
        }
    }

    public function assessAnswer(Answer $answer): AnswerAssessment
    {
        if (!$this->statusAllowsForAssessment()) {
            throw new \UnexpectedValueException('Status do not allows assessment');
        }

        $entry = $this->findAnswerEntry($answer);

        if ($entry === null) {
            throw new \UnexpectedValueException('AnswerEntry not found');
        }

        $assessment = $entry->getCorrectAnswer()->compare($answer);

        $entry->setLastUserAnswerCorrect($assessment->isCorrect());
        $entry->setLastUserAnswer($answer);

        if ($this->status === ExerciseStatus::NEW) {
            $this->setStatus(ExerciseStatus::IN_PROGRESS);
        }
        if ($assessment->isCorrect()) {
            $this->setStatus(ExerciseStatus::DONE);
        }

        return $assessment;
    }

    public function getAnswerEntries(): array
    {
        return $this->answer_entries;
    }

    public function getUpdatedEntries(): array
    {
        return array_filter($this->answer_entries, fn(AnswerEntry $entry) => $entry->isUpdated());
    }

    private function statusAllowsForAssessment(): bool
    {
        return in_array($this->status, [ExerciseStatus::NEW, ExerciseStatus::IN_PROGRESS], true);
    }

    public function findAnswerEntry(Answer $answer): ?AnswerEntry
    {
        foreach ($this->answer_entries as $entry) {
            if ($entry->matches($answer)) {
                return $entry;
            }
        }

        return null;
    }
}