<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Traits;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;

trait HasOwnerBuilder
{
    public function buildOwner(?string $user_id, ?string $admin_id): Owner
    {
        if ($user_id) {
            return new Owner(new OwnerId($user_id), FlashcardOwnerType::USER);
        }
        if ($admin_id) {
            return new Owner(new OwnerId($admin_id), FlashcardOwnerType::ADMIN);
        }

        throw new \UnexpectedValueException('User_id and admin_id cannot be both null');
    }
}
