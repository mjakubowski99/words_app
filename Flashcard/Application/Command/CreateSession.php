<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\User\IUser;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\LearningSessionType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;

final readonly class CreateSession
{
    private Owner $owner;

    public function __construct(
        IUser $user,
        private int $cards_per_session,
        private string $device,
        private ?CategoryId $category_id,
        private LearningSessionType $learning_session_type,
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

    public function hasCategoryId(): bool
    {
        return $this->category_id !== null;
    }

    public function getCategoryId(): CategoryId
    {
        return $this->category_id;
    }

    public function getLearningSessionType(): LearningSessionType
    {
        return $this->learning_session_type;
    }
}
