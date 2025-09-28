<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Services\FlashcardPollManager;

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
