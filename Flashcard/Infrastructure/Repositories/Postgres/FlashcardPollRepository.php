<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\Models\LeitnerLevelUpdate;
use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardPollMapper;

class FlashcardPollRepository implements IFlashcardPollRepository
{
    public function __construct(private FlashcardPollMapper $mapper) {}

    public function findByUser(UserId $user_id, int $learnt_cards_purge_limit): FlashcardPoll
    {
        $poll = $this->mapper->findByUser($user_id, $learnt_cards_purge_limit);

        return $poll ?: new FlashcardPoll($user_id, 0);
    }

    public function saveLeitnerLevelUpdate(LeitnerLevelUpdate $update): void
    {
        $this->mapper->saveLeitnerLevelUpdate($update);
    }

    public function save(FlashcardPoll $poll): void
    {
        $this->mapper->save($poll);
    }

    public function selectNextLeitnerFlashcard(UserId $user_id, int $limit): array
    {
        return $this->mapper->selectNextLeitnerFlashcard($user_id, $limit);
    }

    public function resetLeitnerLevelIfMaxLevelExceeded(UserId $user_id, int $max_level): void
    {
        $this->mapper->resetLeitnerLevelIfMaxLevelExceeded($user_id, $max_level);
    }
}
