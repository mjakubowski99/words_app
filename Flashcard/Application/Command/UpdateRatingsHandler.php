<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\ISessionRepository;
use Shared\Enum\SessionStatus;
use Shared\Flashcard\ISessionFlashcardRating;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\IActiveSessionFlashcardsRepository;

class UpdateRatingsHandler
{
    public function __construct(
        private readonly IActiveSessionFlashcardsRepository $session_flashcards_repository,
        private readonly ISessionRepository                 $session_repository,
        private readonly IRepetitionAlgorithm               $repetition_algorithm,
    ) {}

    public function handle(array $session_flashcard_ratings): void
    {
        $session_flashcard_ids = $this->extractSessionFlashcardIds($session_flashcard_ratings);

        $session_flashcards = $this->session_flashcards_repository->findBySessionFlashcardIds($session_flashcard_ids);

        foreach ($session_flashcard_ratings as $session_flashcard_rating) {
            $session_flashcard_id = new SessionFlashcardId($session_flashcard_rating->getSessionFlashcardId());

            if (!$session_flashcards->get($session_flashcard_id)) {
                continue;
            }

            $session_flashcards->rate($session_flashcard_id, $session_flashcard_rating->getRating());
        }

        $this->session_flashcards_repository->save($session_flashcards);

        $this->repetition_algorithm->handle($session_flashcards);

        if ( !empty($session_flashcard_ids = $session_flashcards->getSessionIdsToFinish()) ) {
            $this->session_repository->updateStatusById($session_flashcard_ids, SessionStatus::FINISHED);
        }
    }

    private function extractSessionFlashcardIds(array $session_flashcard_ratings): array
    {
        return array_map(
            fn (ISessionFlashcardRating $rating) => new SessionFlashcardId($rating->getSessionFlashcardId()),
            $session_flashcard_ratings
        );
    }
}
