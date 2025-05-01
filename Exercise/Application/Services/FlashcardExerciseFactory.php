<?php

declare(strict_types=1);

namespace Exercise\Application\Services;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\SessionFlashcardId;
use Shared\Enum\ExerciseType;
use Shared\Exercise\IExerciseSummary;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

class FlashcardExerciseFactory
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $unscramble_word_repository
    ) {}

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    public function makeExercise(array $session_flashcards_summary, UserId $user_id, ExerciseType $type): IExerciseSummary
    {
        switch ($type) {
            case ExerciseType::UNSCRAMBLE_WORDS:
                return $this->makeUnscrambleWordExercise($session_flashcards_summary, $user_id);
            default:
                throw new \InvalidArgumentException('Invalid exercise type');
        }
    }

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    private function makeUnscrambleWordExercise(array $session_flashcards_summary, UserId $user_id): IExerciseSummary
    {
        $exercise = UnscrambleWordsExercise::newExercise(
            $user_id,
            new SessionFlashcardId($session_flashcards_summary[0]->getSessionFlashcardId()),
            $session_flashcards_summary[0]->getBackWord(),
            $session_flashcards_summary[0]->getFrontContext(),
            $session_flashcards_summary[0]->getFrontWord(),
            $session_flashcards_summary[0]->getEmoji(),
        );

        $exercise_id = $this->unscramble_word_repository->create($exercise);

        return new class($exercise_id) implements IExerciseSummary
        {
            public function __construct(
                private ExerciseId $id,
            ) {}

            public function getId(): ExerciseId
            {
                return $this->id;
            }

            public function getExerciseType(): ExerciseType
            {
                return ExerciseType::UNSCRAMBLE_WORDS;
            }
        };
    }
}