<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\User\IUser;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;

final readonly class CreateSession
{
    private Owner $owner;

    public function __construct(
        IUser $user,
        private int $cards_per_session,
        private string $device,
        private CategoryId $category_id,
    ) {
        $this->owner = new Owner(
            new OwnerId($user->getId()->getValue()),
            FlashcardOwnerType::USER
        );
    }

    public function getOwner(): Owner
    {
        return $this->owner;
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
