<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Exceptions\SessionFinishedException;
use Flashcard\Domain\Exceptions\RateableSessionFlashcardNotFound;

class RateableSessionFlashcards
{
    public function __construct(
        private SessionId $session_id,
        private UserId $user_id,
        private SessionStatus $status,
        private int $rated_count,
        private int $total_count,
        private array $rateable_session_flashcards,
    ) {
        if ($this->status === SessionStatus::FINISHED) {
            throw new SessionFinishedException();
        }
    }

    /** @return RateableSessionFlashcard[] */
    public function getRateableSessionFlashcards(): array
    {
        return $this->rateable_session_flashcards;
    }

    public function all(): array
    {
        return $this->rateable_session_flashcards;
    }

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function isEmpty(): bool
    {
        return count($this->rateable_session_flashcards) === 0;
    }

    public function rate(SessionFlashcardId $id, Rating $rating): void
    {
        $key = $this->findKeyById($id);

        $this->rateable_session_flashcards[$key]->rate($rating);

        ++$this->rated_count;

        if ($this->rated_count === $this->total_count) {
            $this->status = SessionStatus::FINISHED;
        }
    }

    private function findKeyById(SessionFlashcardId $id): int
    {
        foreach ($this->rateable_session_flashcards as $key => $session_flashcard) {
            if ($session_flashcard->getId()->equals($id)) {
                return $key;
            }
        }

        throw new RateableSessionFlashcardNotFound('Flashcard already rated or not exists', (string) $id->getValue());
    }
}
