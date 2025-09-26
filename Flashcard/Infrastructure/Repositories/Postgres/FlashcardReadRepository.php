<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\Language;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardReadMapper;

class FlashcardReadRepository implements IFlashcardReadRepository
{
    public function __construct(private FlashcardReadMapper $mapper) {}

    public function findStatsByUser(UserId $user_id, Language $front_lang, Language $back_lang, ?FlashcardOwnerType $owner_type): RatingStatsReadCollection
    {
        return $this->mapper->findFlashcardStats($front_lang, $back_lang, null, $user_id, $owner_type);
    }

    public function findByUser(UserId $user_id, Language $front_lang, Language $back_lang, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        return $this->mapper->getByUser($user_id, $front_lang, $back_lang, $search, $page, $per_page);
    }
}
