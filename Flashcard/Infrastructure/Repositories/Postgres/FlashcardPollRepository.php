<?php

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardPollMapper;
use Shared\Utils\ValueObjects\UserId;

class FlashcardPollRepository implements IFlashcardPollRepository
{

    public function __construct(private FlashcardPollMapper $mapper)
    {

    }
    public function findByUser(UserId $user_id, int $learnt_cards_purge_limit): FlashcardPoll
    {
        $poll = $this->mapper->findByUser($user_id, $learnt_cards_purge_limit);

        return $poll ?: new FlashcardPoll($user_id, 0);
    }

    public function incrementEasyRatingsCountAndLeitnerLevel(UserId $user_id, array $flashcard_ids, int $leitner_step): void
    {
        $this->mapper->incrementEasyRatingsCountAndLeitnerLevel($user_id, $flashcard_ids, $leitner_step);
    }

    public function save(FlashcardPoll $poll): void
    {
        $this->mapper->save($poll);
    }

    public function selectNextLeitnerFlashcard(UserId $user_id, int $limit): array
    {
        return $this->mapper->selectNextLeitnerFlashcard($user_id, $limit);
    }


    public function resetLeitnerLevelIfNeeded(UserId $user_id): void
    {
        $this->mapper->resetLeitnerLevelIfNeeded($user_id);
    }
}