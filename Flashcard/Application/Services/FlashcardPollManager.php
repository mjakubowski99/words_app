<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPoll;
use Shared\Utils\ValueObjects\UserId;

class FlashcardPollManager
{
    public const int LEARNT_CARDS_PURGE_LIMIT = 10;

    public function __construct(
        private IFlashcardSelector $selector,
        private readonly IFlashcardPollRepository $repository,
    ) {}

    public function refresh(UserId $user_id): FlashcardPoll
    {
        $poll = $this->repository->findByUser(
            $user_id,
            self::LEARNT_CARDS_PURGE_LIMIT
        );

        if (!$poll->pollIsFull()) {
            $flashcard_ids = array_map(
                fn(Flashcard $flashcard) => $flashcard->getId(),
                $this->selector->selectToPoll($user_id, $poll->countToFillPoll())
            );

            $poll->push($flashcard_ids);
        } else if ($poll->areFlashcardsToPurge()) {
            $limit = $poll->getCountToPurge();
            $flashcard_ids = array_map(
                fn(Flashcard $flashcard) => $flashcard->getId(),
                $this->selector->selectToPoll($user_id, $limit)
            );
            $poll->replaceWithNew($flashcard_ids);
        }

        $this->repository->save($poll);

        $this->repository->resetLeitnerLevelIfNeeded($user_id);

        return $poll;
    }
}