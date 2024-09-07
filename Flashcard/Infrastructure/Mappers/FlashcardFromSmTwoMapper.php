<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\CategoryId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\FlashcardId;

class FlashcardFromSmTwoMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, int $limit): array
    {
        return $this->db::table('sm_two_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->where('sm_two_flashcards.user_id', $user_id)
            ->orderBy('sm_two_flashcards.repetition_interval', 'ASC')
            ->take($limit)
            ->select('id', 'word', 'word_lang', 'translation', 'translation_lang', 'context', 'context_translation')
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    public function getFlashcardsWithLowestRepetitionIntervalByCategory(UserId $user_id, CategoryId $category_id, int $limit): array
    {
        return $this->db::table('sm_two_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->where('flashcards.flashcard_category_id', $category_id->getValue())
            ->orderBy('sm_two_flashcards.repetition_interval', 'ASC')
            ->take($limit)
            ->select('id', 'word', 'word_lang', 'translation', 'translation_lang', 'context', 'context_translation')
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    private function map(object $data): Flashcard
    {
        return new Flashcard(
            new FlashcardId($data->id),
            $data->word,
            Language::from($data->word_lang),
            $data->translation,
            Language::from($data->translation_lang),
            $data->context,
            $data->context_translation,
        );
    }
}
