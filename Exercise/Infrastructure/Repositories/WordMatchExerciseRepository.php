<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\WordMatchExercise;
use Exercise\Application\Repositories\IWordMatchExerciseRepository;
use Exercise\Infrastructure\Mappers\Postgres\WordMatchExerciseMapper;

class WordMatchExerciseRepository implements IWordMatchExerciseRepository
{
    public function __construct(private readonly WordMatchExerciseMapper $mapper) {}

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
