<?php

namespace Flashcard\Application\Command;

use Flashcard\Application\Services\FlashcardPollManager;
use Shared\Utils\ValueObjects\UserId;

class ClearFlashcardPoll
{
    public function __construct(
        private FlashcardPollManager $poll_manager,
    ) {}

    public function clear(UserId $user_id): void
    {
        $this->poll_manager->clear($user_id);
    }
}