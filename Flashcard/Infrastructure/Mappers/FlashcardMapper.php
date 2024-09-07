<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\CategoryId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\FlashcardId;

class FlashcardMapper
{
    public function __construct(private readonly DB $db) {}

    public function getRandomFlashcards(UserId $id, int $limit): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.user_id', $id->getValue())
            ->take($limit)
            ->inRandomOrder()
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_category_id', $id->getValue())
            ->take($limit)
            ->inRandomOrder()
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function map(object $data): Flashcard
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
