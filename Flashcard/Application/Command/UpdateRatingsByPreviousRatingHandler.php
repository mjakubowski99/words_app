<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Rating;
use Flashcard\Application\Repository\ISessionFlashcardsRepository;

class UpdateRatingsByPreviousRatingHandler
{
    public function __construct(
        private ISessionFlashcardsRepository $session_flashcards_repository,
    ) {}

    public function handle(array $session_flashcard_ids): void
    {
        $flashcards = $this->session_flashcards_repository->findBySessionFlashcardIds($session_flashcard_ids);

        $ratings = $this->session_flashcards_repository->findLatestRatings($session_flashcard_ids);

        foreach ($flashcards->all() as $flashcard) {
            $flashcard->rate(
                $ratings[$flashcard->getFlashcardId()->getValue()] ?? Rating::UNKNOWN
            );
        }

        $this->session_flashcards_repository->save($flashcards);
    }
}
