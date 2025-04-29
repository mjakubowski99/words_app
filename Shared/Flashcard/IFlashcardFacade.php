<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Utils\ValueObjects\UserId;

interface IFlashcardFacade
{
    public function hasAnySession(UserId $user_id): bool;

    public function deleteUserData(UserId $user_id): void;

    public function updateRatings(array $session_flashcard_ratings): void;

    public function updateRatingsByPreviousRates(array $session_flashcard_id): void;
}
