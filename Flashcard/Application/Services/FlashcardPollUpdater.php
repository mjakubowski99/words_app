<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Domain\Models\LeitnerLevelUpdate;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Application\Repository\IFlashcardPollRepository;

class FlashcardPollUpdater
{
    public function __construct(
        private readonly IFlashcardPollRepository $poll_repository,
    ) {}

    public function handle(RateableSessionFlashcards $session_flashcards): void
    {
        foreach ($session_flashcards->getRateableSessionFlashcards() as $session_flashcard) {
            if ($session_flashcard->rated()) {
                $update = new LeitnerLevelUpdate(
                    $session_flashcards->getUserId(),
                    FlashcardIdCollection::fromArray([$session_flashcard->getFlashcardId()]),
                    $session_flashcard->getRating()->leitnerLevel(),
                );

                $this->poll_repository->saveLeitnerLevelUpdate($update);
            }
        }
    }
}
