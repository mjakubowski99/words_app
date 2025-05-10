<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Exercise\IExerciseScore;
use Shared\Utils\ValueObjects\UserId;

interface IFlashcardFacade
{
    public function hasAnySession(UserId $user_id): bool;

    public function deleteUserData(UserId $user_id): void;

    /** @param IExerciseScore[] $scores */
    public function updateRatings(array $scores): void;

    /** @param int[] $session_flashcard_ids*/
    public function updateRatingsByPreviousRates(array $session_flashcard_ids): void;
}
