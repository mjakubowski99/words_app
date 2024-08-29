<?php

namespace Flashcard\Domain\Services\SmTwo;



use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Domain\Services\IRepetitionAlgorithm;

class SmTwoRepetitionAlgorithm implements IRepetitionAlgorithm
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository
    ) {}

    /** @param SessionFlashcard[] $session_flashcards*/
    public function handle(array $session_flashcards): void
    {
        if (count($session_flashcards) === 0) {
            return;
        }

        $user_id = $session_flashcards[0]->getUserId();
        $flashcard_ids = array_map(fn(SessionFlashcard $flashcard) => $flashcard->getFlashcard()->getId(), $session_flashcards);

        $sm_two_flashcards = $this->repository->findMany($user_id, $flashcard_ids);

        $i=0;
        $sm_two_flashcards = array_map(function (SmTwoFlashcard $flashcard) use ($session_flashcards, &$i){
            $flashcard = $this->handleFlashcard($flashcard, $session_flashcards[$i]->getRating());
            $i++;
            return $flashcard;
        }, $sm_two_flashcards);

        $this->repository->saveMany($sm_two_flashcards);
    }

    public function handleFlashcard(SmTwoFlashcard $sm_two_flashcard, Rating $rating): SmTwoFlashcard
    {
        [$repetition_interval, $repetition_count] = $this->calculateRepetitionInterval(
            $sm_two_flashcard->getRepetitionCount(),
            $sm_two_flashcard->getRepetitionInterval(),
            $sm_two_flashcard->getRepetitionRatio(),
            $rating,
        );

        $repetition_ratio = $this->calculateRepetitionRatio($sm_two_flashcard->getRepetitionRatio(), $rating);

        $sm_two_flashcard->setRepetitionInterval($repetition_interval);
        $sm_two_flashcard->setRepetitionCount($repetition_count);
        $sm_two_flashcard->setRepetitionRatio($repetition_ratio);

        return $sm_two_flashcard;
    }

    private function calculateRepetitionInterval(int $repetition_count, float $repetition_interval, float $repetition_ratio, Rating $rating): array
    {
        if ($rating->value >= Rating::GOOD->value) {
            if ($repetition_count === 0) {
                $repetition_interval = 1;
            } else if ($repetition_count === 1) {
                $repetition_interval  = 6;
            } else {
                $repetition_interval = $repetition_interval  * $repetition_ratio;
            }
            $repetition_count++;
        } else {
            $repetition_interval = 1;
            $repetition_count = 0;
        }

        return [$repetition_interval, $repetition_count];
    }

    private function calculateRepetitionRatio(float $repetition_ratio, Rating $rating): float
    {
        $repetition_ratio = $repetition_ratio + (0.1 - (3 - $rating->value) * (0.08 + (3 - $rating->value) * 0.02));

        if ($repetition_ratio < 1.3) {
            $repetition_ratio = 1.3;
        }

        return $repetition_ratio;
    }
}