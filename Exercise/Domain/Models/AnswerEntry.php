<?php

namespace Exercise\Domain\Models;

use Exercise\Domain\ValueObjects\AnswerEntryId;
use Exercise\Domain\ValueObjects\ExerciseId;
use Exercise\Domain\ValueObjects\SessionFlashcardId;

class AnswerEntry
{
    public function __construct(
        private AnswerEntryId $id,
        private ExerciseId $exercise_id,
        private Answer $correct_answer,
        private ?Answer $last_user_answer,
        private ?bool $last_answer_correct,
        private ?SessionFlashcardId $session_flashcard_id = null,
        private bool $updated = false,
    ) {}

    public function getId(): AnswerEntryId
    {
        return $this->id;
    }

    public function getSessionFlashcardId(): SessionFlashcardId
    {
        return $this->session_flashcard_id;
    }

    public function getExerciseId(): ExerciseId
    {
        return $this->exercise_id;
    }

    public function getCorrectAnswer(): Answer
    {
        return $this->correct_answer;
    }

    public function getLastUserAnswer(): Answer
    {
        return $this->last_user_answer;
    }

    public function isLastAnswerCorrect(): bool
    {
        return $this->last_answer_correct;
    }

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    public function setLastUserAnswer(Answer $answer): void
    {
        $this->last_user_answer = $answer;
        $this->updated = true;
    }

    public function setLastUserAnswerCorrect(bool $is_correct): void
    {
        $this->last_answer_correct = $is_correct;
        $this->updated = true;
    }
}