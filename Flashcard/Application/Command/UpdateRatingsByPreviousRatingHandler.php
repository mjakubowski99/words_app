<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Application\Repository\IActiveSessionRepository;

class UpdateRatingsByPreviousRatingHandler
{
    public function __construct(
        private IActiveSessionRepository $session_flashcards_repository,
    ) {}

    public function handle(array $exercise_entry_ids): void
    {
        $sessions = $this->session_flashcards_repository->findByExerciseEntryIds($exercise_entry_ids);

        foreach ($sessions as $session) {
            $session_flashcard_ids = array_map(
                fn (ActiveSessionFlashcard $flashcard) => $flashcard->getSessionFlashcardId(),
                $session->getSessionFlashcards()
            );

            $ratings = $this->session_flashcards_repository->findLatestRatings($session_flashcard_ids);

            foreach ($session->getSessionFlashcards() as $flashcard) {
                $session->rate(
                    $flashcard->getSessionFlashcardId(),
                    $ratings[$flashcard->getFlashcardId()->getValue()] ?? Rating::UNKNOWN
                );
            }

            $this->session_flashcards_repository->save($session);
        }
    }
}
