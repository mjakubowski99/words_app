<?php

declare(strict_types=1);

namespace Flashcard\Domain\Contracts;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardCategoryType;
use Flashcard\Domain\ValueObjects\CategoryId;

interface ICategory
{
    public function getId(): CategoryId;

    public function getCategoryType(): FlashcardCategoryType;

    public function hasOwner(): bool;

    public function getOwner(): Owner;

    public function getName(): string;
}
