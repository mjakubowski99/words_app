<?php

declare(strict_types=1);

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\Exercise;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Flashcard\IFlashcardFacade;

class UnscrambleWordExerciseAnswerHandler extends AbstractExerciseAnswerHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
        private IFlashcardFacade $facade,
    ) {
        parent::__construct($this->facade);
    }

    protected function resolveExercise(ExerciseEntryId $id): Exercise
    {
        return $this->repository->findByEntryId($id);
    }

    protected function save(Exercise $exercise): void
    {
        if (!$exercise instanceof UnscrambleWordsExercise) {
            throw new \InvalidArgumentException('Invalid exercise type');
        }
        $this->repository->save($exercise);
    }
}
