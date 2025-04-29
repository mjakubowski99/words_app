<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Application\Services\FlashcardPollUpdater;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private FlashcardPollUpdater $poll_updater,
    ) {}

    public function handle(RateableSessionFlashcards $session_flashcards): void
    {
        if ($session_flashcards->isEmpty()) {
            return;
        }

        $flashcard_ids = [];

        /** @var RateableSessionFlashcard $session_flashcard */
        foreach ($session_flashcards->all() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                $flashcard_ids[] = $session_flashcard->getFlashcardId()->getValue();
            }
        }

        $sm_two_flashcards = $this->repository->findMany($session_flashcards->getUserId(), $flashcard_ids);

        /** @var RateableSessionFlashcard $session_flashcard */
        foreach ($session_flashcards->all() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                $sm_two_flashcards->fillIfMissing($session_flashcards->getUserId(), $session_flashcard->getFlashcardId());

                $sm_two_flashcards->updateByRating(
                    $session_flashcard->getFlashcardId(),
                    $session_flashcard->getRating(),
                );
            }
        }

        $this->repository->saveMany($sm_two_flashcards);

        if ($session_flashcards->hasFlashcardPoll()) {
            $this->poll_updater->handle($session_flashcards);
        }
    }

    public function updateByRatings(array $ratings)
    {

    }
}
