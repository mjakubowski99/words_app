<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres\Builders;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardId;

class LearningSessionFlashcardQueryBuilder extends CustomQueryBuilder
{
    public static function tableName(): string
    {
        return 'learning_session_flashcards';
    }

    public function joinLearningSessions(): self
    {
        return $this->leftJoin(
            'learning_sessions',
            'learning_session_flashcards.learning_session_id',
            '=',
            'learning_sessions.id'
        );
    }

    public function byFlashcardId(FlashcardId $id): self
    {
        return $this->where('learning_session_flashcards.flashcard_id', $id->getValue());
    }

    public function byUser(UserId $user_id): self
    {
        return $this->where('learning_sessions.user_id', $user_id->getValue());
    }

    public function notRated(): self
    {
        return $this->whereNull('rating');
    }

    public function rated(): self
    {
        return $this->whereNotNull('rating');
    }

    public function groupByFlashcard(): self
    {
        return $this->groupBy('learning_session_flashcards.flashcard_id');
    }

    public function onlyLatestByFlashcards(int $count): self
    {
        return $this->whereIn('learning_session_flashcards.id', function ($query) use ($count) {
            $query->select('id')
                ->from('learning_session_flashcards as lsf2')
                ->whereColumn('lsf2.flashcard_id', 'learning_session_flashcards.flashcard_id')
                ->orderByDesc('lsf2.id')
                ->limit($count);
        });
    }

    public function addSelectColumn(string $column): self
    {
        return $this->addSelect('learning_session_flashcards.' . $column);
    }

    public function addSelectAvgRating(string $alias): self
    {
        return $this->addSelect(DB::raw("AVG(learning_session_flashcards.rating)::float as {$alias}"));
    }
}
