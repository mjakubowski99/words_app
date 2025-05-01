<?php

namespace Exercise\Application\ReadModels;

use Shared\Exercise\IUnscrambleWordExerciseRead;
use Shared\Utils\ValueObjects\ExerciseId;

class UnscrambleWordExerciseRead implements IUnscrambleWordExerciseRead
{
    public function __construct(
        private ExerciseId $id,
        private string $scrambled_word,
        private string $front_word,
        private string $context_sentence,
        private string $emoji,
        private int $exercise_entry_id,
    ) {}

    public function getId(): ExerciseId
    {
        return $this->id;
    }

    public function getScrambledWord(): string
    {
        return $this->scrambled_word;
    }

    public function getFrontWord(): string
    {
        return $this->front_word;
    }

    public function getContextSentence(): string
    {
        return $this->context_sentence;
    }

    public function getEmoji(): string
    {
        return $this->emoji;
    }

    public function getKeyboard(): array
    {
        $words = explode('', $this->scrambled_word);

        shuffle($words);

        return $words;
    }

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }
}