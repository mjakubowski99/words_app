<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;
use Flashcard\Domain\Models\Rating;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public const int LEITNER_MAX_LEVEL = Rating::VERY_GOOD->value;

    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private IFlashcardPollRepository $poll_repository,
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

                $this->poll_repository->incrementEasyRatingsCountAndLeitnerLevel(
                    $session_flashcards->getUserId(),
                    [$session_flashcard->getFlashcardId()],
                    $session_flashcard->getRating()->leitnerLevel(),
                );
            }
        }

        $this->repository->saveMany($sm_two_flashcards);
    }
}
