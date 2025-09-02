<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Contracts\IRepetitionAlgorithmDTO;
use Flashcard\Domain\Exceptions\SessionFlashcardAlreadyRatedException;

class ActiveSession implements IRepetitionAlgorithmDTO
{
    public function __construct(
        private readonly SessionId $session_id,
        private readonly UserId $user_id,
        private readonly int $max_count,
        private int $rated_count,
        private readonly bool $has_deck,
        private array $session_flashcards = [],
    ) {}

    public function addSessionFlashcard(ActiveSessionFlashcard $flashcard): void
    {
        $this->session_flashcards[$flashcard->getSessionFlashcardId()->getValue()] = $flashcard;
    }

    public function get(SessionFlashcardId $id): ?ActiveSessionFlashcard
    {
        if (!array_key_exists($id->getValue(), $this->session_flashcards)) {
            return null;
        }

        return $this->session_flashcards[$id->getValue()];
    }

    public function isFinished(): bool
    {
        return $this->rated_count >= $this->max_count;
    }

    public function rate(SessionFlashcardId $id, Rating $rating): void
    {
        $flashcard = $this->get($id);

        if ($flashcard->rated()) {
            throw new SessionFlashcardAlreadyRatedException('Already rated', (string) $id->getValue());
        }
        if ($this->rated_count >= $this->max_count) {
            throw new \UnexpectedValueException('Cannot rate flashcard, max count reached');
        }

        $flashcard->rate($rating);

        if (!$flashcard->isAdditional()) {
            ++$this->rated_count;
        }
    }

    public function rateFlashcardsByExerciseScore(SessionFlashcardId $id, float $score): void
    {
        $flashcard = $this->get($id);

        if (!$flashcard->hasExercise()) {
            throw new \UnexpectedValueException('Cannot add score to flashcard without exercise');
        }
        if ($flashcard->rated()) {
            throw new SessionFlashcardAlreadyRatedException('Already rated', (string) $id->getValue());
        }
        if ($this->rated_count >= $this->max_count) {
            throw new \UnexpectedValueException('Cannot rate flashcard, max count reached');
        }

        $flashcard->rate(Rating::fromScore($score));

        if (!$flashcard->isAdditional()) {
            ++$this->rated_count;
        }
    }

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getMaxCount(): int
    {
        return $this->max_count;
    }

    /** @return ActiveSessionFlashcard[] */
    public function getSessionFlashcards(): array
    {
        return array_values($this->session_flashcards);
    }

    public function getRatedCount(): int
    {
        return $this->rated_count;
    }

    public function hasDeck(): bool
    {
        return $this->has_deck;
    }

    public function getUserIdForFlashcard(SessionFlashcardId $id): UserId
    {
        return $this->user_id;
    }

    public function getRatedSessionFlashcardIds(): array
    {
        return array_map(
            fn (ActiveSessionFlashcard $flashcard) => $flashcard->getSessionFlashcardId(),
            array_values(array_filter($this->session_flashcards, fn (ActiveSessionFlashcard $flashcard) => $flashcard->rated())),
        );
    }

    public function getFlashcardId(SessionFlashcardId $id): FlashcardId
    {
        return $this->get($id)->getFlashcardId();
    }

    public function getFlashcardRating(SessionFlashcardId $id): Rating
    {
        return $this->get($id)->getRating();
    }

    public function updatePoll(SessionFlashcardId $id): bool
    {
        return !$this->hasDeck();
    }
}
