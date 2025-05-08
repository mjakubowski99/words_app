<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\ActiveSession;
use Flashcard\Domain\Models\Rating;

interface IActiveSessionRepository
{
    /** @return ActiveSession[] */
    public function findByExerciseEntryIds(array $exercise_entry_ids): array;

    public function save(ActiveSession $session): void;

    /** @return array<int,Rating> */
    public function findLatestRatings(array $session_flashcard_ids): array;
}
