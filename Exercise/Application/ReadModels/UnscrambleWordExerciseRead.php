<?php

declare(strict_types=1);

namespace Exercise\Application\ReadModels;

use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Exercise\IUnscrambleWordExerciseRead;

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
        $word = mb_strtolower($this->scrambled_word);

        $words = mb_str_split($word);

        $words = array_filter($words, function ($word) {
            return !ctype_space($word);
        });

        shuffle($words);

        return $words;
    }

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }
}
