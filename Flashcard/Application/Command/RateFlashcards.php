<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Services\IRepetitionAlgorithm;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class RateFlashcards
{
    public function __construct(
        private readonly ISessionFlashcardRepository $session_flashcard_repository,
        private readonly IRepetitionAlgorithm $repetition_algorithm,
    ) {}

    public function handle(RateFlashcardsCommand $command): void
    {
        $session_flashcard_ids = $this->pluckSessionFlashcardIds($command);

        $session_flashcards = $this->session_flashcard_repository->findMany($command->getSessionId(), $session_flashcard_ids);

        foreach ($command->getRatings() as $rating) {
            $session_flashcards->rate($rating->getSessionFlashcardId(), $rating->getRating());
        }

        $this->session_flashcard_repository->saveRating($session_flashcards);

        $this->repetition_algorithm->handle($session_flashcards);
    }

    /** @return SessionFlashcardId[] */
    private function pluckSessionFlashcardIds(RateFlashcardsCommand $command): array
    {
        return array_map(fn (FlashcardRating $rating) => $rating->getSessionFlashcardId(), $command->getRatings());
    }
}
