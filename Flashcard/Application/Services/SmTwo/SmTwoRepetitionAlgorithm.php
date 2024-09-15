<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Flashcard\Application\Repository\ISmTwoFlashcardRepository;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Domain\Models\RateableSessionFlashcards;

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

        $flashcard_ids = $session_flashcards->pluckSessionFlashcardIds();

        $sm_two_flashcards = $this->repository->findMany($session_flashcards->getOwner(), $flashcard_ids);

        $sm_two_flashcards->fillMissing($session_flashcards->getOwner(), $flashcard_ids);

        foreach ($session_flashcards->all() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                $sm_two_flashcards->updateByRating(
                    $session_flashcard->getFlashcardId(),
                    $session_flashcard->getRating(),
                );
            }
        }

        $this->repository->saveMany($sm_two_flashcards);
    }
}
