<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Repositories;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Infrastructure\Mappers\Postgres\UnscrambleWordExerciseMapper;
use Shared\Utils\ValueObjects\ExerciseId;

class UnscrambleWordExerciseRepository implements IUnscrambleWordExerciseRepository
{
    public function __construct(
        private UnscrambleWordExerciseMapper $mapper
    ) {}

    public function find(ExerciseId $id): UnscrambleWordsExercise
    {
        return $this->mapper->find($id);
    }

    public function findByEntryId(ExerciseEntryId $id): UnscrambleWordsExercise
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
