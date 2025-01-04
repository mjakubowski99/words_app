<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\OwnerId;

class Owner
{
    public function __construct(
        private OwnerId $id,
        private FlashcardOwnerType $flashcard_owner_type,
    ) {}

    public static function fromUser(UserId $user_id): self
    {
        $owner_id = new OwnerId($user_id->getValue());

        return new self($owner_id, FlashcardOwnerType::USER);
    }

    public function getId(): OwnerId
    {
        return $this->id;
    }

    public function getOwnerType(): FlashcardOwnerType
    {
        return $this->flashcard_owner_type;
    }

    public function equals(Owner $owner): bool
    {
        return $this->id->equals($owner->getId())
            && $this->flashcard_owner_type === $owner->getOwnerType();
    }

    public function isUser(): bool
    {
        return $this->flashcard_owner_type === FlashcardOwnerType::USER;
    }

    public function isAdmin(): bool
    {
        return $this->flashcard_owner_type === FlashcardOwnerType::ADMIN;
    }
}
