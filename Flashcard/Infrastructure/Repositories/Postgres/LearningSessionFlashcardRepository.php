<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\GeneralRating;
use Flashcard\Application\Repository\ILearningSessionFlashcardRepository;

class LearningSessionFlashcardRepository implements ILearningSessionFlashcardRepository
{
    /** @return GeneralRating[] */
    public function getFlashcardRatings(SessionId $id): array
    {
        $ratings = DB::table('learning_session_flashcards')
            ->where('learning_session_id', $id)
            ->whereNull('exercise_entry_id')
            ->pluck('rating');

        $results = [];
        foreach ($ratings as $rating) {
            $results[] = new GeneralRating($rating);
        }

        return $results;
    }

    public function getExerciseEntryIds(SessionId $id): array
    {
        return DB::table('learning_session_flashcards')
            ->where('learning_session_id', $id)
            ->whereNotNull('exercise_entry_id')
            ->pluck('exercise_entry_id')
            ->toArray();
    }
}
