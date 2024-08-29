<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\DatabaseRepositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Infrastructure\DatabaseMappers\FlashcardCategoryMapper;
use Illuminate\Support\Facades\DB;
use Shared\Utils\Str\IStr;

class FlashcardCategoryRepository extends AbstractRepository implements IFlashcardCategoryRepository
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
                ...$this->dbPrefix(
                    'flashcard_categories',
                    FlashcardCategoryMapper::COLUMNS
                )
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