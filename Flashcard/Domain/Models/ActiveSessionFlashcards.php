<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Contracts\IRepetitionAlgorithmDTO;

class ActiveSessionFlashcards implements IRepetitionAlgorithmDTO
{
    private array $updated_ratings = [];

    /** @property ActiveSessionFlashcard[] $full_session_flashcards */
    public function __construct(
        private array $active_session_flashcards,
    ) {
        $this->filterFinished();
        $this->indexFlashcards();
    }

    // Core functionality
    public function rate(SessionFlashcardId $session_flashcard_id, Rating $rating): void
    {
        if ($this->get($session_flashcard_id)->rated()) {
            throw new \UnexpectedValueException('Cannot rate already rated flashcard');
        }
        $this->active_session_flashcards[$session_flashcard_id->getValue()]->rate($rating);
        $this->saveUpdatedRatingFact($session_flashcard_id);
    }

    // Getters for collections
    /** @return ActiveSessionFlashcard[] */
    public function all(): array
    {
        return $this->active_session_flashcards;
    }

    public function get(SessionFlashcardId $session_flashcard_id): ?ActiveSessionFlashcard
    {
        if (!array_key_exists($session_flashcard_id->getValue(), $this->active_session_flashcards)) {
            return null;
        }

        return $this->active_session_flashcards[$session_flashcard_id->getValue()];
    }

    public function getRatedSessionFlashcardIds(): array
    {
        $rated_flashcards = array_filter($this->active_session_flashcards, fn (ActiveSessionFlashcard $flashcard) => $flashcard->rated());
        $rated_flashcards = array_values($rated_flashcards);
        return array_map(fn (ActiveSessionFlashcard $flashcard) => $flashcard->getSessionFlashcardId(), $rated_flashcards);
    }

    public function getUpdatedRatings(): array
    {
        return $this->updated_ratings;
    }

    public function getSessionIdsToFinish(): array
    {
        $data = [];
        foreach ($this->active_session_flashcards as $session_flashcard) {
            $data[$session_flashcard->getSessionId()->getValue()] = [$session_flashcard->getRatedCount(), $session_flashcard->getMaxCount()];
        }

        $session_ids = [];
        foreach ($data as $session_id => $flashcard_data) {
            if (!array_key_exists($session_id, $this->updated_ratings)) {
                continue;
            }

            if ($this->updated_ratings[$session_id] + $flashcard_data[0] >= $flashcard_data[1]) {
                $session_ids[] = $session_id;
            }
        }

        return $session_ids;
    }

    // Individual flashcard getters (IRepetitionAlgorithmDTO implementation)
    public function getUserIdForFlashcard(SessionFlashcardId $id): UserId
    {
        return $this->active_session_flashcards[$id->getValue()]->getUserId();
    }

    public function getFlashcardRating(SessionFlashcardId $id): Rating
    {
        return $this->active_session_flashcards[$id->getValue()]->getRating();
    }

    public function getFlashcardId(SessionFlashcardId $id): FlashcardId
    {
        return $this->active_session_flashcards[$id->getValue()]->getFlashcardId();
    }

    public function updatePoll(SessionFlashcardId $id): bool
    {
        return $this->active_session_flashcards[$id->getValue()]->hasDeck();
    }

    // Private helpers
    private function filterFinished(): void
    {
        $this->active_session_flashcards = array_filter(
            $this->active_session_flashcards,
            fn (ActiveSessionFlashcard $flashcard) => $flashcard->getSessionStatus() !== SessionStatus::FINISHED
        );
    }

    private function indexFlashcards(): void
    {
        $indexed = [];
        foreach ($this->active_session_flashcards as $session_flashcard) {
            $indexed[$session_flashcard->getSessionFlashcardId()->getValue()] = $session_flashcard;
        }
        $this->active_session_flashcards = $indexed;
    }

    private function saveUpdatedRatingFact(SessionFlashcardId $session_flashcard_id): void
    {
        $flashcard = $this->get($session_flashcard_id);
        if (!$flashcard || !$flashcard->rated()) {
            return;
        }
        $session_id = $flashcard->getSessionId()->getValue();

        if (!array_key_exists($session_id, $this->updated_ratings)) {
            $this->updated_ratings[$session_id] = 0;
        }
        $this->updated_ratings[$session_id] += 1;
    }
}