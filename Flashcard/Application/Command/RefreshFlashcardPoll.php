<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Services\FlashcardPollManager;
use Shared\Utils\ValueObjects\UserId;

class RefreshFlashcardPoll
{
    public function __construct(
        private FlashcardPollManager $poll_manager,
    ) {}

    public function refresh(UserId $user_id): void
    {
        $this->poll_manager->refresh($user_id);
    }
}