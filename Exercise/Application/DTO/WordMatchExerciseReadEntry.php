<?php

namespace Exercise\Application\DTO;

use Shared\Exercise\Exercises\IWordMatchExerciseReadEntry;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class WordMatchExerciseReadEntry implements IWordMatchExerciseReadEntry
{
    public function __construct(
        private ExerciseEntryId $exercise_entry_id,
        private string $word,
        private string $word_translation,
        private string $sentence
    ) {}

    public function getExerciseEntryId(): ExerciseEntryId
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

    public function getSentencePartBeforeWord(): string
    {
        if ($this->getSentencePartAfterWord()) {
            return $this->getSentence();
        }

        $parts = explode($this->word, $this->sentence, 2);

        return $parts[0] ?? '';
    }

    public function getSentencePartAfterWord(): string
    {
        $parts = explode($this->word, $this->sentence, 2);
        return $parts[1] ?? '';
    }
}