<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Exceptions\InvalidSmTwoFlashcardSetException;

class SmTwoFlashcards implements \Countable
{
    /** @throws InvalidSmTwoFlashcardSetException */
    public function __construct(private array $sm_two_flashcards)
    {
        $this->validate();
    }

    public function fillMissing(UserId $user_id, array $flashcard_ids): void
    {
        foreach ($flashcard_ids as $flashcard_id) {
            if (!$this->searchKeyByUserFlashcard($flashcard_id)) {
                $this->sm_two_flashcards[] = new SmTwoFlashcard($user_id, $flashcard_id);
            }
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
            if (!$sm_two_flashcard->getUserId()->equals($to_compare->getUserId())) {
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
