<?php

namespace Flashcard\Infrastructure\DatabaseMappers;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardId;
use Shared\Utils\ValueObjects\Language;

class FlashcardMapper
{
    public const COLUMNS = ['id', 'word', 'word_lang', 'translation', 'translation_lang', 'context', 'context_translation'];

    public function map(array $data): Flashcard
    {
        return new Flashcard(
            new FlashcardId((string) $data['flashcards_id']),
            $data['flashcards_word'],
            Language::from($data['flashcards_word_lang']),
            $data['flashcards_translation'],
            Language::from($data['flashcards_translation_lang']),
            $data['flashcards_context'],
            $data['flashcards_context_translation'],
        );
    }
}