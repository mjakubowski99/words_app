<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;

class SessionService
{
    public function __construct(
        private readonly ISessionRepository $session_repository,
        private readonly IFlashcardCategoryRepository $category_repository,
    ) {}

    public function newSession(
        Owner $owner,
        CategoryId $category_id,
        int $cards_per_session,
        string $device
    ): Session {
        $this->session_repository->setAllOwnerSessionsStatus($owner, SessionStatus::STARTED);

        $category = $this->category_repository->findById($category_id);

        return new Session(
            SessionStatus::STARTED,
            $owner,
            $cards_per_session,
            $device,
            $category
        );
    }
}
