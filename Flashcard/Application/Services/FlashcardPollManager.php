<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Application\Repository\IFlashcardPollRepository;

class FlashcardPollManager
{
    public const int LEITNER_MAX_LEVEL = 30000;

    public function __construct(
        private IFlashcardSelector $selector,
        private readonly IFlashcardPollRepository $repository,
        private readonly FlashcardPollResolver $resolver,
    ) {}

    public function refresh(UserId $user_id, Language $front, Language $back): FlashcardPoll
    {
        $poll = $this->resolver->resolve($user_id);

        if (!$poll->pollIsFull()) {
            $flashcard_ids = array_map(
                fn (Flashcard $flashcard) => $flashcard->getId(),
                $this->selector->selectToPoll($user_id, $poll->countToFillPoll(), $front->getEnum(), $back->getEnum())
            );

            $poll->push(FlashcardIdCollection::fromArray($flashcard_ids));
        } elseif ($poll->areFlashcardsToPurge()) {
            $limit = $poll->getCountToPurge();
            $flashcard_ids = array_map(
                fn (Flashcard $flashcard) => $flashcard->getId(),
                $this->selector->selectToPoll($user_id, $limit, $front->getEnum(), $back->getEnum())
            );
            $poll->replaceWithNew(FlashcardIdCollection::fromArray($flashcard_ids));
        }

        $this->repository->save($poll);

        $this->repository->resetLeitnerLevelIfMaxLevelExceeded($user_id, self::LEITNER_MAX_LEVEL);

        return $poll;
    }

    public function clear(UserId $user_id): void
    {
        $this->repository->deleteAllByUserId($user_id);
    }
}
