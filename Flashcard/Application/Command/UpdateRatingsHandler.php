<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Flashcard\ISessionFlashcardRating;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\ISessionFlashcardsRepository;

class UpdateRatingsHandler
{
    public function __construct(
        private readonly ISessionFlashcardsRepository $session_flashcards_repository,
        private readonly IRepetitionAlgorithm $repetition_algorithm,
    ) {}

    public function handle(array $session_flashcard_ratings): void
    {
        $session_flashcard_ids = array_map(
            fn (ISessionFlashcardRating $rating) => new SessionFlashcardId($rating->getSessionFlashcardId()),
            $session_flashcard_ratings
        );

        $session_flashcards = $this->session_flashcards_repository->findBySessionFlashcardIds($session_flashcard_ids);

        foreach ($session_flashcard_ratings as $session_flashcard_rating) {

            $session_flashcard = $session_flashcards->get(
                new SessionFlashcardId($session_flashcard_rating->getSessionFlashcardId())
            );

            if ($session_flashcard) {
                $session_flashcards->rate(
                    new SessionFlashcardId($session_flashcard_rating->getSessionFlashcardId()),
                    $session_flashcard_rating->getRating()
                );
            }
        }

        $this->session_flashcards_repository->save($session_flashcards);

        $this->repetition_algorithm->handle($session_flashcards);
    }
}
