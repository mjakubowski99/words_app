<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres\Builders;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class LearningSessionQueryBuilder extends CustomQueryBuilder
{
    public static function tableName(): string
    {
        return 'learning_sessions';
    }

    public function byDeckId(FlashcardDeckId $id): self
    {
        return $this->where('learning_sessions.flashcard_deck_id', $id->getValue());
    }
}
