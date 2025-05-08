<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\Repository\IActiveSessionRepository;
use Flashcard\Domain\Models\ActiveSession;
use Flashcard\Domain\Models\Rating;
use Flashcard\Infrastructure\Mappers\Postgres\ActiveSessionFlashcardsMapper;

class ActiveSessionRepository implements IActiveSessionRepository
{
    public function __construct(
        private readonly ActiveSessionFlashcardsMapper $mapper
    ) {}

    public function findByExerciseEntryIds(array $exercise_entry_ids): array
    {
        return $this->mapper->findByExerciseEntryIds($exercise_entry_ids);
    }

    public function save(ActiveSession $session): void
    {
        $this->mapper->save($session);
    }

    /** @return array<int,Rating> */
    public function findLatestRatings(array $session_flashcard_ids): array
    {
        return $this->mapper->findLatestRatings($session_flashcard_ids);
    }
}
