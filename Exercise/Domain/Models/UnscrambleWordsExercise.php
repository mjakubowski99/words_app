<?php

namespace Exercise\Domain\Models;

use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\ValueObjects\ExerciseId;
use Exercise\Domain\ValueObjects\SessionFlashcardId;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;

class UnscrambleWordsExercise extends Exercise
{
    public function __construct(
        ExerciseId            $id,
        private UserId        $user_id,
        ExerciseStatus        $status,
        ExerciseEntryId       $answer_entry_id,
        ?SessionFlashcardId   $session_flashcard_id,
        private string        $word,
        private string        $context_sentence,
        private string        $word_translation,
        private string        $emoji,
        private string        $scrambled_word,
        ?UnscrambleWordAnswer $last_answer,
        ?bool                 $last_answer_correct,
    ) {
        $entry = new ExerciseEntry(
            $answer_entry_id,
            $id,
            new UnscrambleWordAnswer($answer_entry_id, $word),
            $last_answer,
            $last_answer_correct
        );

        parent::__construct($id, [$entry], $status, ExerciseType::UNSCRAMBLE_WORDS);
    }

    public static function newExercise(
        UserId $user_id,
        SessionFlashcardId $session_flashcard_id,
        string $word,
        string $context_sentence,
        string $word_translation,
        string $emoji,
    ): self
    {
        $scrambled_word = implode("", shuffle(str_split($word)));

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

    public function getUserId(): UserId
    {
        return $this->user_id;
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