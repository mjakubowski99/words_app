<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Services\IRepetitionAlgorithm;

class RateFlashcards
{
    public function __construct(
        private ISessionRepository $session_repository,
        private IRepetitionAlgorithm $repetition_algorithm,
    ) {}

    public function handle(RateFlashcardsCommand $command): void
    {
        $session_flashcard_ids = $this->pluckSessionFlashcardIds($command);

        $session_flashcards = $this->session_repository->findManySessionFlashcards($session_flashcard_ids);

        foreach ($command->getRatings() as $rating) {
            $session_flashcard = $session_flashcards->findById($rating->getSessionFlashcardId());
            $session_flashcard->rate($rating->getRating());
        }

        $this->session_repository->bulkSaveSessionFlashcards($session_flashcards->all());

        $this->repetition_algorithm->handle($session_flashcards->all());
    }

    /** @return SessionFlashcardId[] */
    private function pluckSessionFlashcardIds(RateFlashcardsCommand $command): array
    {
        return array_map(fn(FlashcardRating $rating) => $rating->getSessionFlashcardId(), $command->getRatings());
    }
}