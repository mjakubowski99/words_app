<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IRateableSessionFlashcardsRepository;
use Flashcard\Application\Services\IRepetitionAlgorithm;

class RateFlashcards
{
    public function __construct(
        private readonly IRateableSessionFlashcardsRepository $repository,
        private readonly IRepetitionAlgorithm $repetition_algorithm,
    ) {}

    public function handle(RateFlashcardsCommand $command): void
    {
        $session_flashcards = $this->repository->find($command->getSessionId());

        foreach ($command->getRatings() as $rating) {
            $session_flashcards->rate($rating->getSessionFlashcardId(), $rating->getRating());
        }

        $this->repository->save($session_flashcards);

        $this->repetition_algorithm->handle($session_flashcards);
    }
}
