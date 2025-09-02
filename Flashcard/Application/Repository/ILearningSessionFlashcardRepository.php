<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\GeneralRating;

interface ILearningSessionFlashcardRepository
{
    /** @return GeneralRating[] */
    public function getFlashcardRatings(SessionId $id): array;

    public function getExerciseEntryIds(SessionId $id): array;
}
