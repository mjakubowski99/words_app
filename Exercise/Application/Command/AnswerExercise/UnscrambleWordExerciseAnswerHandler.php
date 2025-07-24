<?php

declare(strict_types=1);

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Domain\Exceptions\InvalidExerciseTypeException;
use Exercise\Domain\Models\Exercise;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\ExerciseEntryId;
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

    public function findExerciseId(ExerciseEntryId $id): ExerciseId
    {
        return $this->repository->findByEntryId($id)->getId();
    }

    protected function resolveExercise(ExerciseId $exercise_id): Exercise
    {
        return $this->repository->find($exercise_id);
    }

    protected function save(Exercise $exercise): void
    {
        if (!$exercise instanceof UnscrambleWordsExercise) {
            throw new InvalidExerciseTypeException();
        }
        $this->repository->save($exercise);
    }
}
