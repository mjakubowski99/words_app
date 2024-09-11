<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\Contracts\ICategory;
use Shared\Enum\FlashcardCategoryType;

class Category implements ICategory
{
    private CategoryId $id;

    public function __construct(
        private Owner $owner,
        private string $tag,
        private string $name,
    ) {}

    public function init(CategoryId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function hasOwner(): bool
    {
        return true;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategoryType(): FlashcardCategoryType
    {
        return FlashcardCategoryType::NORMAL;
    }
}
