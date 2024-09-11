<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services\SmTwo;

use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Services\IRepetitionAlgorithm;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository
    ) {}

    public function handle(SessionFlashcards $session_flashcards): void
    {
        if ($session_flashcards->isEmpty()) {
            return;
        }

        $flashcard_ids = $session_flashcards->pluckFlashcardIds();

        $sm_two_flashcards = $this->repository->findMany(
            $session_flashcards->getSession()->getOwner(),
            $flashcard_ids
        );

        $sm_two_flashcards->fillMissing($session_flashcards->getSession()->getOwner(), $flashcard_ids);

        foreach ($session_flashcards->all() as $session_flashcard) {
            $sm_two_flashcards->updateByRating(
                $session_flashcard->getFlashcardId(),
                $session_flashcard->getRating(),
            );
        }

        $this->repository->saveMany($sm_two_flashcards);
    }
}
