<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\FlashcardPoll;
use Shared\Utils\ValueObjects\UserId;

interface IFlashcardPollRepository
{
    public function findByUser(UserId $user_id, int $learnt_cards_purge_limit): FlashcardPoll;
    public function selectNextLeitnerFlashcard(UserId $user_id, int $limit);
    public function incrementEasyRatingsCountAndLeitnerLevel(UserId $user_id, array $flashcard_ids, int $leitner_step): void;
    public function save(FlashcardPoll $poll): void;
    public function resetLeitnerLevelIfNeeded(UserId $user_id): void;
}