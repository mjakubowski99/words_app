<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Enum\FlashcardOwnerType;
use Shared\User\IUser;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

class GetUserRatingStats
{
    public function __construct(private readonly IFlashcardReadRepository $repository) {}

    public function get(IUser $user, ?FlashcardOwnerType $owner_type): RatingStatsReadCollection
    {
        return $this->repository->findStatsByUser(
            $user->getId(),
            $user->getUserLanguage()->getEnum(),
            $user->getLearningLanguage()->getEnum(),
            $owner_type
        );
    }
}
