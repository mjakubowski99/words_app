<?php

namespace Flashcard\Domain\Services\SmTwo;



use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Domain\Services\IRepetitionAlgorithm;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository
    ) {}

    public function handle(SessionFlashcards $session_flashcards): void
    {
        if ($session_flashcards->isEmpty()) {
            return;
        }

        $user_id = $session_flashcards->getUserId();
        $flashcard_ids = $session_flashcards->pluckFlashcardIds();

        $sm_two_flashcards = $this->repository->findMany($user_id, $flashcard_ids);

        foreach ($session_flashcards->all() as $session_flashcard) {
            $sm_two_flashcards->updateByRating(
                $session_flashcard->getFlashcardId(),
                $session_flashcard->getRating(),
            );
        }

        $this->repository->saveMany($sm_two_flashcards);
    }
}