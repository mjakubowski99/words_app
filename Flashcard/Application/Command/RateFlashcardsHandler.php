<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\IRateableSessionFlashcardsRepository;
use Shared\Exceptions\UnauthorizedException;

class RateFlashcardsHandler
{
    public function __construct(
        private readonly IRateableSessionFlashcardsRepository $repository,
        private readonly IRepetitionAlgorithm $repetition_algorithm,
    ) {}

    public function handle(RateFlashcards $command): void
    {
        $session_flashcards = $this->repository->find($command->getSessionId());

        if ($session_flashcards->getUserId()->equals($command->getUserId())) {
            throw new UnauthorizedException('User is not session owner');
        }

        foreach ($command->getRatings() as $rating) {
            $session_flashcards->rate($rating->getSessionFlashcardId(), $rating->getRating());
        }

        $this->repository->save($session_flashcards);

        $this->repetition_algorithm->handle($session_flashcards);
    }
}
