<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\FlashcardCategoryType;
use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\Exceptions\NotImplementedException;

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

    public function canCreateSession(Owner $owner): bool
    {
        return true;
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
