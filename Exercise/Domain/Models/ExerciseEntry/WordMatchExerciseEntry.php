<?php

declare(strict_types=1);

namespace Exercise\Domain\Models\ExerciseEntry;

use Exercise\Domain\Models\Answer\Answer;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\Answer\WordMatchAnswer;

class WordMatchExerciseEntry extends ExerciseEntry
{
    public function __construct(
        private string $word,
        private string $word_translation,
        private string $sentence,
        ExerciseEntryId $id,
        ExerciseId $exercise_id,
        Answer $correct_answer,
        ?Answer $last_user_answer,
        ?bool $last_answer_correct,
        int $order,
        float $score = 0.0,
        int $answers_count = 0,
    ) {
        parent::__construct(
            $id,
            $exercise_id,
            $correct_answer,
            $last_user_answer,
            $last_answer_correct,
            $order,
            $score,
            $answers_count,
        );
    }

    public static function newFromSummary(ISessionFlashcardSummary $summary): self
    {
        return new self(
            $summary->getBackWord(),
            $summary->getFrontWord(),
            $summary->getStorySentence() ?? $summary->getBackContext(),
            ExerciseEntryId::noId(),
            ExerciseId::noId(),
            WordMatchAnswer::fromString(ExerciseEntryId::noId(), $summary->getBackWord()),
            null,
            null,
            $summary->getOrder(),
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

    public function getSentence(): string
    {
        return $this->sentence;
    }
}
