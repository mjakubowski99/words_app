<?php

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\ValueObjects\ExerciseId;
use Exercise\Infrastructure\Mappers\UnscrambleWordExerciseMapper;

class UnscrambleWordExerciseRepository implements IUnscrambleWordExerciseRepository
{
    public function __construct(
        private UnscrambleWordExerciseMapper $mapper
    ) {}

    public function find(ExerciseId $id): UnscrambleWordsExercise
    {
        return $this->mapper->find($id);
    }

    public function findByAnswerEntryId(ExerciseEntryId $id): UnscrambleWordsExercise
    {
        return $this->mapper->findByEntryId($id);
    }

   public function create(UnscrambleWordsExercise $exercise): ExerciseId
    {
        return $this->mapper->create($exercise);
    }

    public function save(UnscrambleWordsExercise $exercise): void
    {
        $this->mapper->save($exercise);
    }
}