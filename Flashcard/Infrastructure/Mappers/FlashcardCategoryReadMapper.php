<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\GeneralRating;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Application\ReadModels\CategoryDetailsRead;

class FlashcardCategoryReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findDetails(CategoryId $id, ?string $search, int $page, int $per_page): CategoryDetailsRead
    {
        $category = $this->db::table('flashcard_categories')->find($id->getValue());

        if (!$category) {
            throw new ModelNotFoundException('Category not found');
        }

        $results = $this->db::table('flashcards')
            ->latest()
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $search = mb_strtolower($search);

                    return $q->where(DB::raw('LOWER(flashcards.word)'), 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('LOWER(flashcards.translation)'), 'LIKE', '%' . $search . '%');
                });
            })
            ->where('flashcards.flashcard_category_id', $id->getValue())
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->selectRaw('
                flashcards.*,
                (SELECT learning_session_flashcards.rating
                 FROM learning_session_flashcards 
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                 ORDER BY learning_session_flashcards.updated_at DESC
                 LIMIT 1
                ) as last_rating
            ')
            ->get()
            ->map(function (object $data) {
                return new FlashcardRead(
                    new FlashcardId($data->id),
                    $data->word,
                    Language::from($data->word_lang),
                    $data->translation,
                    Language::from($data->translation_lang),
                    $data->context,
                    $data->context_translation,
                    new GeneralRating($data->last_rating)
                );
            })->toArray();

        return new CategoryDetailsRead(
            new CategoryId($category->id),
            $category->name,
            $results
        );
    }

    public function getByOwner(Owner $owner, ?string $search, int $page, int $per_page): array
    {
        return $this->db::table('flashcard_categories')
            ->where('flashcard_categories.user_id', $owner->getId())
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(DB::raw('LOWER(flashcard_categories.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
            })
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->latest()
            ->get()
            ->map(function (object $data) {
                return new OwnerCategoryRead(
                    new CategoryId($data->id),
                    $data->name,
                );
            })->toArray();
    }
}
