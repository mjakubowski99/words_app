<?php

declare(strict_types=1);

namespace Exercise\Application\DTO;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IWordMatchExerciseReadEntry;

class WordMatchExerciseReadEntry implements IWordMatchExerciseReadEntry
{
    public function __construct(
        private ExerciseEntryId $exercise_entry_id,
        private bool $answered,
        private string $word,
        private string $word_translation,
        private string $sentence
    ) {}

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->exercise_entry_id;
    }

    public function isAnswered(): bool
    {
        return $this->answered;
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

    public function getSentencePartBeforeWord(): string
    {
        $word = preg_quote($this->word, '/');

        $parts = preg_split("/\\b{$word}\\b/ui", $this->sentence, 2);

        return $parts[0] ?? '';
    }

    public function getSentencePartAfterWord(): string
    {
        $word = preg_quote($this->word, '/');

        $parts = preg_split("/\\b{$word}\\b/ui", $this->sentence, 2);

        return $parts[1] ?? '';
    }
}
