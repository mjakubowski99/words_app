<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\UserId;

class FlashcardCategory
{
    public const MAIN = 'main';

    private CategoryId $id;

    public function __construct(
        private ?UserId $user_id,
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

    public function hasUserId(): UserId
    {
        return $this->user_id;
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
