<?php

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;

class FlashcardCategory
{
    public const MAIN = 'main';

    public function __construct(
        private UserId $user_id,
        private string $tag,
        private string $name,
    ) {}

    public function setCategoryId(CategoryId $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function getId(): CategoryId
    {
        return $this->category_id;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function isMainCategory(): bool
    {
        return $this->tag == self::MAIN;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }
}