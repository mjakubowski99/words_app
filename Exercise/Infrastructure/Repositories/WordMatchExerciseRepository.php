<?php

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\IWordMatchExerciseRepository;
use Exercise\Domain\Models\WordMatchExercise;
use Exercise\Infrastructure\Mappers\Postgres\WordMatchExerciseMapper;
use Shared\Utils\ValueObjects\ExerciseId;

class WordMatchExerciseRepository implements IWordMatchExerciseRepository
{
    public function __construct(private readonly WordMatchExerciseMapper $mapper)
    {

    }
    public function find(ExerciseId $id): WordMatchExercise
    {
        return $this->mapper->find($id);
    }

    public function create(WordMatchExercise $exercise): ExerciseId
    {
        return $this->mapper->create($exercise);
    }

    public function save(WordMatchExercise $exercise): void
    {
        $this->mapper->save($exercise);
    }
}