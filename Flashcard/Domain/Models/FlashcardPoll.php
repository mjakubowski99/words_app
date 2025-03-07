<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Types\FlashcardIdCollection;

class FlashcardPoll
{
    private const int POLL_LIMIT = 30;
    private const int EASY_REPETITIONS_COUNT_TO_PURGE = 3;
    private FlashcardIdCollection $flashcard_ids_to_purge;

    public function __construct(
        private readonly UserId $user_id,
        private readonly int $poll_size,
        private FlashcardIdCollection $purge_candidates = new FlashcardIdCollection(),
        private FlashcardIdCollection $flashcard_ids_to_add = new FlashcardIdCollection(),
    ) {
        if ($this->poll_size > $this->getPollLimit()) {
            throw new \UnexpectedValueException('Poll size cannot be bigger than');
        }

        $this->flashcard_ids_to_purge = new FlashcardIdCollection();
    }

    public function getEasyRepetitionsCountToPurge(): int
    {
        return self::EASY_REPETITIONS_COUNT_TO_PURGE;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function replaceWithNew(FlashcardIdCollection $flashcard_ids): void
    {
        if (!$this->areFlashcardsToPurge() && !$this->pollIsFull()) {
            return;
        }

        $i = 0;
        foreach ($this->getPurgeCandidates() as $flashcard_to_reject) {
            if ($flashcard_ids[$i]) {
                $this->replace($flashcard_to_reject, $flashcard_ids[$i]);
            }
            ++$i;
        }
    }

    public function push(FlashcardId|FlashcardIdCollection $id): void
    {
        if (!is_iterable($id)) {
            $id = [$id];
        }

        foreach ($id as $i) {
            if ($this->canAddNext()) {
                $this->flashcard_ids_to_add[] = $i;
            }
        }
    }

    private function replace(FlashcardId $old, FlashcardId $new): void
    {
        $this->flashcard_ids_to_add[] = $new;
        $this->flashcard_ids_to_purge[] = $old;
    }

    public function countToFillPoll(): int
    {
        return $this->getPollLimit() - $this->poll_size;
    }

    public function canAddNext(): bool
    {
        return $this->poll_size + 1 < $this->getPollLimit();
    }

    public function pollIsFull(): bool
    {
        return !$this->canAddNext();
    }

    public function areFlashcardsToPurge(): bool
    {
        return $this->getCountToPurge() > 0;
    }

    public function getCountToPurge(): int
    {
        return count($this->purge_candidates);
    }

    public function getPurgeCandidates(): FlashcardIdCollection
    {
        return $this->purge_candidates;
    }

    public function getFlashcardIdsToPurge(): FlashcardIdCollection
    {
        return $this->flashcard_ids_to_purge;
    }

    public function getFlashcardIdsToAdd(): FlashcardIdCollection
    {
        return $this->flashcard_ids_to_add;
    }

    public function getPollLimit(): int
    {
        return self::POLL_LIMIT;
    }
}
