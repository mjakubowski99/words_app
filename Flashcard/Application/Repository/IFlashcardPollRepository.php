<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\Models\LeitnerLevelUpdate;

interface IFlashcardPollRepository
{
    public function findByUser(UserId $user_id, int $learnt_cards_purge_limit): FlashcardPoll;

    public function purgeLatestFlashcards(UserId $user_id, int $limit): void;

    public function selectNextLeitnerFlashcard(UserId $user_id, array $exclude_flashcard_ids, int $limit): array;

    public function saveLeitnerLevelUpdate(LeitnerLevelUpdate $update): void;

    public function save(FlashcardPoll $poll): void;

    public function resetLeitnerLevelIfMaxLevelExceeded(UserId $user_id, int $max_level): void;
    public function deleteAllByUserId(UserId $user_id): void;
}
