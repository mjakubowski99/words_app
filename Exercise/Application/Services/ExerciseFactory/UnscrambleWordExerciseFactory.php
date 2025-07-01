<?php

namespace Exercise\Application\Services\ExerciseFactory;

use Exercise\Application\DTO\FlashcardExercise;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\UserId;

class UnscrambleWordExerciseFactory implements IExerciseFactory
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $unscramble_word_repository,
    ) {}

    public function make(ISessionFlashcardSummaries $summaries, UserId $user_id): array
    {
        $exercise = UnscrambleWordsExercise::newExercise(
            $user_id,
            $summaries->getSummaries()[0]->getBackWord(),
            $summaries->getSummaries()[0]->getFrontContext(),
            $summaries->getSummaries()[0]->getFrontWord(),
            $summaries->getSummaries()[0]->getEmoji(),
        );

        $exercise_id = $this->unscramble_word_repository->create($exercise);

        $exercise = $this->unscramble_word_repository->find($exercise_id);

        return FlashcardExercise::newCollection($summaries, $exercise);
    }
}