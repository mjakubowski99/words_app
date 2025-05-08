<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Utils\ValueObjects\UserId;

interface IFlashcardFacade
{
    public function hasAnySession(UserId $user_id): bool;

    public function deleteUserData(UserId $user_id): void;

    /** @param IExerciseScore[] $session_flashcard_ratings */
    public function updateRatings(array $session_flashcard_ratings): void;

    /** @param int[] $session_flashcard_ids*/
    public function updateRatingsByPreviousRates(array $session_flashcard_ids): void;
}
