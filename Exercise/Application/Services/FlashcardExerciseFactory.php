<?php

declare(strict_types=1);

namespace Exercise\Application\Services;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\UserId;
use Shared\Exercise\IFlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;

class FlashcardExerciseFactory
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $unscramble_word_repository
    ) {}

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    public function makeExercise(array $session_flashcards_summary, UserId $user_id, ExerciseType $type): array
    {
        return match ($type) {
            /* @phpstan-ignore-next-line */
            ExerciseType::UNSCRAMBLE_WORDS => $this->makeUnscrambleWordExercise($session_flashcards_summary, $user_id),
            default => throw new \InvalidArgumentException('Invalid exercise type'),
        };
    }

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    private function makeUnscrambleWordExercise(array $session_flashcards_summary, UserId $user_id): array
    {
        $exercise = UnscrambleWordsExercise::newExercise(
            $user_id,
            $session_flashcards_summary[0]->getBackWord(),
            $session_flashcards_summary[0]->getBackContext(),
            $session_flashcards_summary[0]->getFrontWord(),
            $session_flashcards_summary[0]->getEmoji(),
        );

        $exercise_id = $this->unscramble_word_repository->create($exercise);

        $exercise = $this->unscramble_word_repository->find($exercise_id);

        $flashcard_id = $session_flashcards_summary[0]->getFlashcardId();

        $entries = new class($exercise, $flashcard_id) implements IFlashcardExercise {
            public function __construct(
                private readonly UnscrambleWordsExercise $exercise,
                private readonly int $flashcard_id,
            ) {}

            public function getExerciseEntryId(): ExerciseEntryId
            {
                return $this->exercise->getExerciseEntries()[0]->getId();
            }

            public function getFlashcardId(): int
            {
                return $this->flashcard_id;
            }
        };

        return [$entries];
    }
}
