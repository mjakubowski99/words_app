<?php

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\UserId;

class FlashcardCategoryDetailsDTO
{
    public function __construct(
        private CategoryId $id,
        private string $name,
        private UserId $user_id,
        private array $flashcards,
    ) {}

    public function getCategoryId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    /** @return Flashcard[] */
    public function getFlashcards(): array
    {
        return $this->flashcards;
    }
}