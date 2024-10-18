<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Illuminate\Support\Facades\Log;
use Flashcard\Domain\Models\RateableSessionFlashcard;
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

        $flashcard_ids = [];

        /** @var RateableSessionFlashcard $session_flashcard */
        foreach ($session_flashcards->all() as $session_flashcard) {
            $flashcard_ids[] = $session_flashcard->getFlashcardId()->getValue();
        }

        $sm_two_flashcards = $this->repository->findMany($session_flashcards->getOwner(), $flashcard_ids);

        /** @var RateableSessionFlashcard $session_flashcard */
        foreach ($session_flashcards->all() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                Log::info((string) count($sm_two_flashcards->all()));

                $sm_two_flashcards->fillIfMissing($session_flashcards->getOwner(), $session_flashcard->getFlashcardId());

                Log::info((string) count($sm_two_flashcards->all()));

                $sm_two_flashcards->updateByRating(
                    $session_flashcard->getFlashcardId(),
                    $session_flashcard->getRating(),
                );
            }
        }

        $this->repository->saveMany($sm_two_flashcards);
    }
}
