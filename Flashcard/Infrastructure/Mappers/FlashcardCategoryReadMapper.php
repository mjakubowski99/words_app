<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Application\ReadModels\CategoryDetailsRead;

class FlashcardCategoryReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findDetails(CategoryId $id): CategoryDetailsRead
    {
        $category = $this->db::table('flashcard_categories')->find($id->getValue());

        if (!$category) {
            throw new ModelNotFoundException('Category not found');
        }

        $results = $this->db::table('flashcards')
            ->where('flashcards.flashcard_category_id', $id->getValue())
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
                );
            })->toArray();

        return new CategoryDetailsRead(
            new CategoryId($category->id),
            $category->name,
            $results
        );
    }

    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        return $this->db::table('flashcard_categories')
            ->where('flashcard_categories.user_id', $owner->getId())
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->get()
            ->map(function (object $data) {
                return new OwnerCategoryRead(
                    new CategoryId($data->id),
                    $data->name,
                );
            })->toArray();
    }
}
