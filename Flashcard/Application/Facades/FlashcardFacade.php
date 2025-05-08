<?php

declare(strict_types=1);

namespace Flashcard\Application\Facades;

use Flashcard\Application\Command\DeleteUserDataHandler;
use Flashcard\Application\Command\UpdateRatingsByPreviousRatingHandler;
use Flashcard\Application\Command\UpdateRatingsHandler;
use Flashcard\Application\Repository\ISessionRepository;
use Shared\Exercise\IExerciseScore;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\UserId;

class FlashcardFacade implements IFlashcardFacade
{
    public function __construct(
        private DeleteUserDataHandler $delete_user_data_handler,
        private ISessionRepository $session_repository,
        private UpdateRatingsHandler $update_ratings_handler,
        private UpdateRatingsByPreviousRatingHandler $update_ratings_by_previous_rating_handler,
    ) {}

    public function deleteUserData(UserId $user_id): void
    {
        $this->delete_user_data_handler->handle($user_id);
    }

    public function hasAnySession(UserId $user_id): bool
    {
        return $this->session_repository->hasAnySession($user_id);
    }

    /** @param IExerciseScore[] $session_flashcard_ratings */
    public function updateRatings(array $session_flashcard_ratings): void
    {
        $this->update_ratings_handler->handle($session_flashcard_ratings);
    }

    /** @param int[] $session_flashcard_ids */
    public function updateRatingsByPreviousRates(array $session_flashcard_ids): void
    {
        $this->update_ratings_by_previous_rating_handler->handle($session_flashcard_ids);
    }
}
