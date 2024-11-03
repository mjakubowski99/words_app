<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;

class FlashcardFromSmTwoMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function getFlashcardsWithLowerRepetitionInterval(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->where('flashcards.user_id', $owner->getId())
            ->leftJoin('flashcard_categories', 'flashcard_categories.id', '=', 'flashcards.flashcard_category_id')
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw("
                CASE 
                    WHEN sm_two_flashcards.updated_at IS NOT NULL AND sm_two_flashcards.repetition_interval IS NOT NULL 
                         AND DATE(sm_two_flashcards.updated_at) + CAST(sm_two_flashcards.repetition_interval AS INTEGER) <= CURRENT_DATE 
                    THEN 1
                    ELSE 0
                END DESC,
                COALESCE(repetition_interval, 1.0) ASC
            ")
            ->select(
                'flashcards.*',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag as category_tag',
                'flashcard_categories.name as category_name',
            )
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    public function getFlashcardsByLowestRepetitionIntervalByCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids): array
    {
        $date = today();

        return $this->db::table('flashcards')
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->leftJoin('flashcard_categories', 'flashcard_categories.id', '=', 'flashcards.flashcard_category_id')
            ->where('flashcards.flashcard_category_id', $category_id->getValue())
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw("
                CASE 
                    WHEN sm_two_flashcards.updated_at IS NOT NULL AND sm_two_flashcards.repetition_interval IS NOT NULL 
                         AND DATE(sm_two_flashcards.updated_at) + CAST(sm_two_flashcards.repetition_interval AS INTEGER) <= CURRENT_DATE
                    THEN 1
                    ELSE 0
                END DESC,
                COALESCE(repetition_interval, 1.0) ASC
            ")
            ->select(
                'flashcards.*',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag as category_tag',
                'flashcard_categories.name as category_name',
            )
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    private function map(object $data): Flashcard
    {
        $category = $data->flashcard_category_id ? (new Category(
            new Owner(new OwnerId($data->category_user_id), FlashcardOwnerType::USER),
            $data->category_tag,
            $data->category_name,
        ))->init(new CategoryId($data->flashcard_category_id)) : null;

        return new Flashcard(
            new FlashcardId($data->id),
            $data->word,
            Language::from($data->word_lang),
            $data->translation,
            Language::from($data->translation_lang),
            $data->context,
            $data->context_translation,
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $category,
        );
    }
}
