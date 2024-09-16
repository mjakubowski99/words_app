<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;

class Owner
{
    public function __construct(
        private OwnerId $id,
        private FlashcardOwnerType $flashcard_owner_type,
    ) {}

    public function getId(): OwnerId
    {
        return $this->id;
    }

    public function getOwnerType(): FlashcardOwnerType
    {
        return $this->flashcard_owner_type;
    }
}
