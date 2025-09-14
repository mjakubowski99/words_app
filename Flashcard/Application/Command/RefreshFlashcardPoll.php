<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Services\FlashcardPollManager;

class RefreshFlashcardPoll
{
    public function __construct(
        private FlashcardPollManager $poll_manager,
    ) {}

    public function refresh(UserId $user_id, Language $front, Language $back): void
    {
        $this->poll_manager->refresh($user_id, $front, $back);
    }
}
