<?php

declare(strict_types=1);

namespace Exercise\Application\ReadModels;

class WordMatchExerciseReadEntry
{
    public function __construct(
        private int $exercise_entry_id,
        private string $word,
        private string $word_translation,
        private string $sentence,
    ) {}

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getWordTranslation(): string
    {
        return $this->word_translation;
    }

    public function getSentence(): string
    {
        return $this->sentence;
    }
}
