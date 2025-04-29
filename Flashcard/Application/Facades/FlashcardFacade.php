<?php

declare(strict_types=1);

namespace Flashcard\Application\Facades;

use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Flashcard\Application\Command\DeleteUserDataHandler;
use Flashcard\Application\Repository\ISessionRepository;

class FlashcardFacade implements IFlashcardFacade
{
    public function __construct(
        private DeleteUserDataHandler $delete_user_data_handler,
        private ISessionRepository $session_repository,
    ) {}

    public function deleteUserData(UserId $user_id): void
    {
        $this->delete_user_data_handler->handle($user_id);
    }

    public function hasAnySession(UserId $user_id): bool
    {
        return $this->session_repository->hasAnySession($user_id);
    }

    public function updateRatings(array $session_flashcard_ratings): void
    {

    }

    public function updateRatingsByPreviousRates(array $session_flashcard_id): void
    {
        // TODO: Implement updateRatingsByPreviousRates() method.
    }
}
