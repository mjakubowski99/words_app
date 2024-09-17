<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository
    ) {}

    public function handle(RateableSessionFlashcards $session_flashcards): void
    {
        if ($session_flashcards->isEmpty()) {
            return;
        }

        $rated_flashcard_ids = [];
        foreach ($session_flashcards->all() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                $rated_flashcard_ids[] = $session_flashcard->getFlashcardId();
            }
        }

        $sm_two_flashcards = $this->repository->findMany($session_flashcards->getOwner(), $rated_flashcard_ids);

        foreach ($session_flashcards->all() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                $sm_two_flashcards->fillMissing($session_flashcards->getOwner(), [$session_flashcard->getFlashcardId()]);

                $sm_two_flashcards->updateByRating(
                    $session_flashcard->getFlashcardId(),
                    $session_flashcard->getRating(),
                );
            }
        }

        $this->repository->saveMany($sm_two_flashcards);
    }
}
