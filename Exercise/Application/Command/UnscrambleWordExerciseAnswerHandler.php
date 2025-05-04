<?php

declare(strict_types=1);

namespace Exercise\Application\Command;

use Exercise\Domain\Models\Exercise;
use Shared\Flashcard\IFlashcardFacade;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;

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
        return $this->repository->findByAnswerEntryId($id);
    }

    protected function save(Exercise $exercise): void
    {
        if (!$exercise instanceof UnscrambleWordsExercise) {
            throw new \InvalidArgumentException('Invalid exercise type');
        }
        $this->repository->save($exercise);
    }
}
