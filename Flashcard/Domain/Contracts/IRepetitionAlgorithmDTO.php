<?php

declare(strict_types=1);

namespace Flashcard\Domain\Contracts;

use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

interface IRepetitionAlgorithmDTO
{
    public function getUserIdForFlashcard(SessionFlashcardId $id): UserId;

    public function getRatedSessionFlashcardIds(): array;

    public function getFlashcardId(SessionFlashcardId $id): FlashcardId;

    public function getFlashcardRating(SessionFlashcardId $id): Rating;

    public function updatePoll(SessionFlashcardId $id): bool;
}
