<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Exceptions\InvalidSmTwoFlashcardSetException;

class SmTwoFlashcards implements \Countable
{
    /** @throws InvalidSmTwoFlashcardSetException */
    public function __construct(private array $sm_two_flashcards)
    {
        $this->validate();
    }

    public function fillIfMissing(Owner $owner, FlashcardId $flashcard_id): void
    {
        if (!$this->searchKeyByUserFlashcard($flashcard_id)) {
            $this->sm_two_flashcards[] = new SmTwoFlashcard($owner, $flashcard_id);
        }
    }

    public function all(): array
    {
        return $this->sm_two_flashcards;
    }

    /** @throws InvalidSmTwoFlashcardSetException */
    public function validate(): void
    {
        if (count($this->sm_two_flashcards) === 0) {
            return;
        }
        $to_compare = $this->sm_two_flashcards[0];

        foreach ($this->sm_two_flashcards as $sm_two_flashcard) {
            if (!$sm_two_flashcard->getOwner()->getId()->equals($to_compare->getOwner()->getId())) {
                throw new InvalidSmTwoFlashcardSetException('Not every flashcard in set has same user id');
            }
        }
    }

    public function updateByRating(FlashcardId $flashcard_id, Rating $rating): void
    {
        $key = $this->searchKeyByUserFlashcard($flashcard_id);
        $this->sm_two_flashcards[$key]->updateByRating($rating);
    }

    private function searchKeyByUserFlashcard(FlashcardId $flashcard_id): ?int
    {
        foreach ($this->sm_two_flashcards as $key => $sm_two_flashcard) {
            if (!$sm_two_flashcard->getFlashcardId()->equals($flashcard_id)) {
                continue;
            }

            return $key;
        }

        return null;
    }

    public function count(): int
    {
        return count($this->all());
    }
}
