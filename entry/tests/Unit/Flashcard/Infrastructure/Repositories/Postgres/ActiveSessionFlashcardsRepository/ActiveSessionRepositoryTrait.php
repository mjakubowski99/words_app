<?php

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionFlashcardsRepository;

use App\Models\Flashcard;
use App\Models\LearningSession;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Illuminate\Support\Arr;
use App\Models\LearningSessionFlashcard;

trait ActiveSessionRepositoryTrait
{
    private function createLearningSession(array $attributes = []): LearningSession
    {
        return LearningSession::factory()->create($attributes);
    }

    private function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    private function createSessionFlashcard(array $attributes = []): LearningSessionFlashcard
    {
        return LearningSessionFlashcard::factory()->create($attributes);
    }

    private function pluckLearningSessionFlashcardId(array $flashcards): array
    {
        return Arr::map($flashcards, fn(LearningSessionFlashcard $f) => $f->getId());
    }

    private function findBySessionFlashcardId(SessionFlashcardId $id, array $session_flashcard_models): ?LearningSessionFlashcard
    {
        return Arr::first(
            $session_flashcard_models,
            fn (LearningSessionFlashcard $model) => $model->getId()->equals($id)
        );
    }
}
