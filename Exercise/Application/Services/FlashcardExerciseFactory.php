<?php

declare(strict_types=1);

namespace Exercise\Application\Services;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\ISessionFlashcardSummary;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\SessionFlashcardId;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;

class FlashcardExerciseFactory
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $unscramble_word_repository
    ) {}

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    public function makeExercise(array $session_flashcards_summary, UserId $user_id, ExerciseType $type): void
    {
        switch ($type) {
            case ExerciseType::UNSCRAMBLE_WORDS:
                $this->makeUnscrambleWordExercise($session_flashcards_summary, $user_id);

                break;

            default:
                throw new \InvalidArgumentException('Invalid exercise type');
        }
    }

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    private function makeUnscrambleWordExercise(array $session_flashcards_summary, UserId $user_id): void
    {
        $exercise = UnscrambleWordsExercise::newExercise(
            $user_id,
            new SessionFlashcardId($session_flashcards_summary[0]->getSessionFlashcardId()),
            $session_flashcards_summary[0]->getBackWord(),
            $session_flashcards_summary[0]->getFrontContext(),
            $session_flashcards_summary[0]->getFrontWord(),
            $session_flashcards_summary[0]->getEmoji(),
        );

        $this->unscramble_word_repository->create($exercise);
    }
}
