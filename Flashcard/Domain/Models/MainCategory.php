<?php

namespace Flashcard\Domain\Models;

use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\Exceptions\NotImplementedException;
use Flashcard\Domain\ValueObjects\CategoryId;
use Shared\Enum\FlashcardCategoryType;

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

    public function getOwner(): Owner
    {
        throw new NotImplementedException();
    }

    public function getName(): string
    {
        return __('Main category');
    }
}