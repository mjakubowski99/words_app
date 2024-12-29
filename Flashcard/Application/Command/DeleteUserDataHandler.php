<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\ISessionRepository;
use Shared\Utils\ValueObjects\UserId;

readonly class DeleteUserDataHandler
{
    public function __construct(
        private ISessionRepository $session_repository,
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function handle(UserId $user_id): void
    {
        $this->session_repository->deleteAllForUser($user_id);
        $this->deck_repository->deleteAllForUser($user_id);
        $this->flashcard_repository->deleteAllForUser($user_id);
    }
}