<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\CategoryId;
use Shared\User\IUser;

final readonly class CreateSession
{
    public function __construct(
        private IUser $user,
        private int $cards_per_session,
        private string $device,
        private CategoryId $category_id,
    ) {}

    public function getOwnerUser(): IUser
    {
        return $this->user;
    }

    public function getCardsPerSession(): int
    {
        return $this->cards_per_session;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function getCategoryId(): CategoryId
    {
        return $this->category_id;
    }
}