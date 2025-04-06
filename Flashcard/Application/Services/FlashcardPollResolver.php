<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Domain\Exceptions\FlashcardPollOverLoadedException;

class FlashcardPollResolver
{
    public const int LEARNT_CARDS_PURGE_LIMIT = 10;

    public function __construct(
        private readonly IFlashcardPollRepository $repository,
    ) {}

    public function resolve(UserId $user_id): FlashcardPoll
    {
        try {
            return $this->repository->findByUser($user_id, self::LEARNT_CARDS_PURGE_LIMIT);
        } catch (FlashcardPollOverLoadedException $exception) {
            $count_to_purge = $exception->getCurrentSize() - $exception->getExpectedMaxSize();

            $this->repository->purgeLatestFlashcards($user_id, $count_to_purge);

            return $this->repository->findByUser($user_id, self::LEARNT_CARDS_PURGE_LIMIT);
        }
    }
}
