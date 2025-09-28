<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\Exercise\WordMatchExercise;
use Exercise\Infrastructure\Mappers\Postgres\WordMatchExerciseMapper;
use Exercise\Application\Repositories\Exercise\IWordMatchExerciseRepository;

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
