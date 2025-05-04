<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\ValueObjects\SessionFlashcardId;

class UnscrambleWordsExercise extends Exercise
{
    public function __construct(
        ExerciseId $id,
        UserId $user_id,
        ExerciseStatus $status,
        ExerciseEntryId $answer_entry_id,
        ?SessionFlashcardId $session_flashcard_id,
        private string $word,
        private string $context_sentence,
        private string $word_translation,
        private string $emoji,
        private string $scrambled_word,
        ?UnscrambleWordAnswer $last_answer,
        ?bool $last_answer_correct,
    ) {
        $entry = new ExerciseEntry(
            $answer_entry_id,
            $id,
            new UnscrambleWordAnswer($answer_entry_id, $word),
            $last_answer,
            $last_answer_correct,
            $session_flashcard_id,
        );

        parent::__construct($id, $user_id, [$entry], $status, ExerciseType::UNSCRAMBLE_WORDS);
    }

    public static function newExercise(
        UserId $user_id,
        SessionFlashcardId $session_flashcard_id,
        string $word,
        string $context_sentence,
        string $word_translation,
        string $emoji,
    ): self {
        $word_arr = mb_str_split($word);
        shuffle($word_arr);
        $scrambled_word = implode('', $word_arr);

        return new self(
            ExerciseId::noId(),
            $user_id,
            ExerciseStatus::NEW,
            ExerciseEntryId::noId(),
            $session_flashcard_id,
            $word,
            $context_sentence,
            $word_translation,
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

    public function getEmoji(): string
    {
        return $this->emoji;
    }

    public function getScrambledWord(): string
    {
        return $this->scrambled_word;
    }
}
