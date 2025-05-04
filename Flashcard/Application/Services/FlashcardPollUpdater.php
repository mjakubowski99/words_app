<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Domain\Models\LeitnerLevelUpdate;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Domain\Contracts\IRepetitionAlgorithmDTO;
use Flashcard\Application\Repository\IFlashcardPollRepository;

class FlashcardPollUpdater
{
    public function __construct(
        private readonly IFlashcardPollRepository $poll_repository,
    ) {}

    public function handle(IRepetitionAlgorithmDTO $dto): void
    {
        foreach ($dto->getRatedSessionFlashcardIds() as $flashcard_id) {
            if (!$dto->updatePoll($flashcard_id)) {
                continue;
            }

            $update = new LeitnerLevelUpdate(
                $dto->getUserIdForFlashcard($flashcard_id),
                FlashcardIdCollection::fromArray([$dto->getFlashcardId($flashcard_id)]),
                $dto->getFlashcardRating($flashcard_id)->leitnerLevel(),
            );

            $this->poll_repository->saveLeitnerLevelUpdate($update);
        }
    }
}
