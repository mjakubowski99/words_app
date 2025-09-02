<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Flashcard\Application\Repository\ILearningSessionFlashcardRepository;

final class GetSessionScoreHandler
{
    public function __construct(
        private readonly ILearningSessionFlashcardRepository $session_repository,
        private readonly IExerciseReadFacade $facade,
    ) {}

    public function handle(SessionId $id): float
    {
        $entry_ids = $this->session_repository->getExerciseEntryIds($id);

        $ratings = $this->session_repository->getFlashcardRatings($id);

        $scores_sum = $this->facade->getExerciseScoreSum($entry_ids);

        $sum = 0.0;
        foreach ($ratings as $rating) {
            $sum += $rating->toScore();
        }

        return ($sum + $scores_sum) / (count($ratings) + count($entry_ids));
    }
}
