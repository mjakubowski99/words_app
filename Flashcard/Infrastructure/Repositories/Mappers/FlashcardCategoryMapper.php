<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Mappers;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;
use Shared\Utils\ValueObjects\UserId;

class FlashcardCategoryMapper
{
    public const COLUMNS = ['id', 'user_id', 'name', 'tag'];

    public function map(array $data): FlashcardCategory
    {
        $category_id = CategoryId::fromInt($data['flashcard_categories_id']);

        $category = new FlashcardCategory(
            UserId::fromString($data['flashcard_categories_user_id']),
            $data['flashcard_categories_tag'],
            $data['flashcard_categories_name'],
        );

        $category->setCategoryId($category_id);

        return $category;
    }
}