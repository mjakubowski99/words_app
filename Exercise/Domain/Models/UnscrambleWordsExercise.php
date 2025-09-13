<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Shared\Models\Emoji;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class UnscrambleWordsExercise extends Exercise
{
    public function __construct(
        ExerciseId $id,
        UserId $user_id,
        ExerciseStatus $status,
        ExerciseEntryId $answer_entry_id,
        private string $word,
        private string $context_sentence,
        private string $word_translation,
        private ?string $context_sentence_translation,
        private ?Emoji $emoji,
        private string $scrambled_word,
        ?UnscrambleWordAnswer $last_answer,
        ?bool $last_answer_correct,
        float $score = 0.0,
        int $answers_count = 0,
    ) {
        $entry = new ExerciseEntry(
            $answer_entry_id,
            $id,
            new UnscrambleWordAnswer($answer_entry_id, $word),
            $last_answer,
            $last_answer_correct,
            0,
            $score,
            $answers_count,
        );

        parent::__construct($id, $user_id, [$entry], $status, ExerciseType::UNSCRAMBLE_WORDS);
    }

    public static function scramble(string $word): string
    {
        $word_arr = mb_str_split($word);
        shuffle($word_arr);

        return implode('', $word_arr);
    }

    public static function newExercise(
        UserId $user_id,
        string $word,
        string $context_sentence,
        string $word_translation,
        string $context_sentence_translation,
        ?Emoji $emoji,
    ): self {
        $scrambled_word = self::scramble($word);

        if ($word === $scrambled_word) {
            $scrambled_word = self::scramble($word);
        }

        return new self(
            ExerciseId::noId(),
            $user_id,
            ExerciseStatus::NEW,
            ExerciseEntryId::noId(),
            $word,
            $context_sentence,
            $word_translation,
            $context_sentence_translation,
            $emoji,
            $scrambled_word,
            null,
            null,
        );
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getWordTranslation(): string
    {
        return $this->word_translation;
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

    public function getScrambledWord(): string
    {
        return $this->scrambled_word;
    }
}
