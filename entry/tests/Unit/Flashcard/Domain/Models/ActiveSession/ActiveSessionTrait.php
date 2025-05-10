<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\ActiveSession;

use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\ActiveSession;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

trait ActiveSessionTrait
{
    public static function scoreDataProvider(): array
    {
        return [
            'good' => [0.8, Rating::GOOD],
            'very_good' => [0.9, Rating::VERY_GOOD],
            'unknown' => [0.2, Rating::UNKNOWN],
            'weak' => [0.4, Rating::WEAK],
        ];
    }

    private function createActiveSession(array $attributes = []): ActiveSession
    {
        return new ActiveSession(
            $attributes['session_id'] ?? new SessionId(1),
            $attributes['user_id'] ?? UserId::new(),
            $attributes['max_count'] ?? 1,
            $attributes['rated_count'] ?? 0,
            $attributes['has_deck'] ?? true
        );
    }

    private function createActiveSessionFlashcard(array $attributes = []): ActiveSessionFlashcard
    {
        return new ActiveSessionFlashcard(
            $attributes['session_flashcard_id'] ?? new SessionFlashcardId(rand(1, 100)),
            $attributes['flashcard_id'] ?? new FlashcardId(3),
            $attributes['rating'] ?? null,
            $attributes['exercise_entry_id'] ?? null,
            $attributes['has_deck'] ?? true
        );
    }
}
