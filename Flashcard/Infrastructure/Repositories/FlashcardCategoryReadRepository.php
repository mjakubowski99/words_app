<?php

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Application\ReadModels\CategoryDetailsRead;
use Flashcard\Application\ReadModels\CategoryRead;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\Repository\IFlashcardCategoryReadRepository;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\Language;

class FlashcardCategoryReadRepository implements IFlashcardCategoryReadRepository
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function find(CategoryId $id): CategoryDetailsRead
    {
        $category = $this->db::table('flashcard_categories')->find($id);

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
            ->skip(($page-1) * $per_page)
            ->get()
            ->map(function (object $data) {
                return new CategoryRead(
                    new CategoryId($data->id),
                    $data->name,
                );
            })->toArray();
    }
}