<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Application\ReadModels\SessionFlashcardRead;

class SessionFlashcardReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findUnratedById(SessionId $session_id, int $limit): array
    {
        return $this->db::table('learning_session_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->where('learning_session_id', $session_id->getValue())
            ->whereNull('rating')
            ->take($limit)
            ->select(
                'learning_session_flashcards.id as id',
                'learning_session_flashcards.rating as rating',
                'flashcards.word',
                'flashcards.word_lang',
                'flashcards.translation',
                'flashcards.translation_lang',
                'flashcards.context',
                'flashcards.context_translation'
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    private function map(object $data): SessionFlashcardRead
    {
        return new SessionFlashcardRead(
            new SessionFlashcardId($data->id),
            $data->word,
            Language::from($data->word_lang),
            $data->translation,
            Language::from($data->translation_lang),
            $data->context,
            $data->context_translation
        );
    }
}
