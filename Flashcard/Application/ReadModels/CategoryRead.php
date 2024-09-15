<?php

namespace Flashcard\Application\ReadModels;

use Flashcard\Domain\ValueObjects\CategoryId;

class CategoryRead
{
    public function __construct(private CategoryId $id, private string $name) {}

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}