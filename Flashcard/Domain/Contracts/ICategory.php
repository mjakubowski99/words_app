<?php

namespace Flashcard\Domain\Contracts;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Shared\Enum\FlashcardCategoryType;

interface ICategory
{
    public function getId(): CategoryId;

    public function getCategoryType(): FlashcardCategoryType;

    public function hasOwner(): bool;

    public function getOwner(): Owner;

    public function getName(): string;
}