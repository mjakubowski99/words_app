<?php

namespace Flashcard\Domain\Models;

class FlashcardCategory
{
    public const MAIN = 'main';

    private CategoryId $category_id;

    public function __construct(
        private string $tag,
        private string $name,
    ) {}

    public static function newMainCategory()
    {

    }

    public function setCategoryId(CategoryId $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function getId(): CategoryId
    {
        return $this->category_id;
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