<?php

declare(strict_types=1);

namespace Exercise\Application\DTO\Exercise;

use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;

class UnscrambleWordExerciseRead implements IUnscrambleWordExerciseRead
{
    public function __construct(
        private ExerciseId $id,
        private string $scrambled_word,
        private string $front_word,
        private string $context_sentence,
        private ?string $context_sentence_translation,
        private string $back_word,
        private ?Emoji $emoji,
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

    public function getBackWord(): string
    {
        return $this->back_word;
    }

    public function getContextSentence(): string
    {
        return $this->context_sentence;
    }

    public function getContextSentenceTranslation(): ?string
    {
        return $this->context_sentence_translation;
    }

    public function getEmoji(): ?Emoji
    {
        return $this->emoji;
    }

    public function getKeyboard(): array
    {
        $traps = [];
        for ($i = 0; $i < 3; ++$i) {
            $traps[] = mb_strtolower(chr(rand(65, 90))); // A-Z (uppercase)
        }

        $word = mb_strtolower($this->scrambled_word);

        $words = mb_str_split($word);

        $words = array_filter($words, function ($word) {
            return !ctype_space($word);
        });

        $words = array_merge($words, $traps);

        shuffle($words);

        return $words;
    }

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }
}
