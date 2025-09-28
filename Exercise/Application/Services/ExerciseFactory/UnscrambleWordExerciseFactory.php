<?php

declare(strict_types=1);

namespace Exercise\Application\Services\ExerciseFactory;

use Shared\Utils\ValueObjects\UserId;
use Exercise\Application\DTO\FlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Exercise\Domain\Models\Exercise\UnscrambleWordsExercise;
use Exercise\Application\Repositories\Exercise\IUnscrambleWordExerciseRepository;

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
            $summaries->getSummaries()[0]->getBackContext(),
            $summaries->getSummaries()[0]->getFrontWord(),
            $summaries->getSummaries()[0]->getFrontContext(),
            $summaries->getSummaries()[0]->getEmoji(),
        );

        $exercise_id = $this->unscramble_word_repository->create($exercise);

        $exercise = $this->unscramble_word_repository->find($exercise_id);

        return FlashcardExercise::fromFlashcardSummaries($summaries, $exercise);
    }
}
