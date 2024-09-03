<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Infrastructure\Repositories\Mappers\FlashcardCategoryMapper;
use Illuminate\Support\Facades\DB;

class FlashcardCategoryRepository implements IFlashcardCategoryRepository
{
    public function __construct(
        private readonly DB $db,
        private FlashcardCategoryMapper $category_mapper,
    ) {}

    public function findById(CategoryId $id): FlashcardCategory
    {
        $db_category = (array) $this->db::table('flashcard_categories')
            ->where('id', $id->getValue())
            ->select(
                'flashcard_categories.id as flashcard_categories_id',
                'flashcard_categories.user_id as flashcard_categories_user_id',
                'flashcard_categories.tag as flashcard_categories_tag',
                'flashcard_categories.name as flashcard_categories_name',
            )
            ->first();

        return $this->category_mapper->map($db_category);
    }

    public function findMain(): FlashcardCategory
    {
        // TODO: Implement findMain() method.
    }

    public function createCategory(FlashcardCategory $category): CategoryId
    {
        // TODO: Implement createCategory() method.
    }
}