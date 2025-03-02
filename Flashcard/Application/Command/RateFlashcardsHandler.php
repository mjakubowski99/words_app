<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Application\Services\FlashcardPollUpdater;
use Flashcard\Domain\Models\LeitnerLevelUpdate;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Shared\Exceptions\UnauthorizedException;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\IRateableSessionFlashcardsRepository;

class RateFlashcardsHandler
{
    public function __construct(
        private readonly IRateableSessionFlashcardsRepository $repository,
        private readonly IRepetitionAlgorithm $repetition_algorithm,
        private readonly FlashcardPollUpdater $poll_updater,
    ) {}

    public function handle(RateFlashcards $command): void
    {
        $session_flashcards = $this->repository->find($command->getSessionId());

        if (!$session_flashcards->getUserId()->equals($command->getUserId())) {
            throw new UnauthorizedException('User is not session owner');
        }

        foreach ($command->getRatings() as $rating) {
            $session_flashcards->rate($rating->getSessionFlashcardId(), $rating->getRating());
        }

        $this->repository->save($session_flashcards);

        $this->repetition_algorithm->handle($session_flashcards);

        $this->poll_updater->handle($session_flashcards);
    }
}
