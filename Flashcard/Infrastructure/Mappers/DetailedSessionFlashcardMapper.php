<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\CategoryId;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SessionId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\DetailedSessionFlashcard;
use Shared\Utils\ValueObjects\UserId;

class DetailedSessionFlashcardMapper
{
    public function __construct(private readonly DB $db) {}

    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): array
    {
        return $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $session_id)
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->whereNull('learning_session_flashcards.rating')
            ->select(
                'learning_session_flashcards.id',
                'learning_session_flashcards.rating',
                'flashcards.id as flashcards_id',
                'flashcards.word',
                'flashcards.word_lang',
                'flashcards.translation',
                'flashcards.translation_lang',
                'flashcards.context',
                'flashcards.context_translation',
                'flashcards.user_id',
                'flashcards.flashcard_category_id',
            )
            ->take($limit)
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function map(object $data): DetailedSessionFlashcard
    {
        return new DetailedSessionFlashcard(
            new SessionFlashcardId($data->id),
            $data->rating ? Rating::from($data->rating) : null,
            new Flashcard(
                new FlashcardId($data->flashcards_id),
                $data->word,
                Language::from($data->word_lang),
                $data->translation,
                Language::from($data->translation_lang),
                $data->context,
                $data->context_translation,
                new UserId($data->user_id),
                new CategoryId($data->flashcard_category_id)
            ),
        );
    }
}
