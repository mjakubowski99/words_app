<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Domain\Repositories\ISessionRepository;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;

class SessionService
{
    public function __construct(
        private readonly ISessionRepository $session_repository,
        private readonly IFlashcardCategoryRepository $category_repository,
    ) {}

    public function newSession(
        UserId $user_id,
        CategoryId $category_id,
        int $cards_per_session,
        string $device
    ): Session
    {
        $this->session_repository->setAllUserSessionsStatus($user_id, SessionStatus::STARTED);

        $category = $this->category_repository->findById($category_id);

        return new Session(
            SessionStatus::STARTED,
            $user_id,
            $cards_per_session,
            $device,
            $category
        );
    }
}