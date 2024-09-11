<?php

namespace Flashcard\Domain\Models;

use Flashcard\Domain\Contracts\ICategory;
use Shared\Enum\FlashcardCategoryType;
use Shared\Utils\ValueObjects\UserId;

class MainCategory implements ICategory
{

    public function getId(): CategoryId
    {
        return new CategoryId(0);
    }

    public function getCategoryType(): FlashcardCategoryType
    {
        return FlashcardCategoryType::GENERAL;
    }

    public function hasOwner(): bool
    {
        return false;
    }

    public function getOwner(): UserId
    {
        throw new \Exception("Not implemented");
    }

    public function getName(): string
    {
        return __('Main category');
    }
}