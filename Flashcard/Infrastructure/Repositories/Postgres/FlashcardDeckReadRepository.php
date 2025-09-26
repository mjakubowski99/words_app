<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardReadMapper;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardDeckReadMapper;

class FlashcardDeckReadRepository implements IFlashcardDeckReadRepository
{
    public function __construct(
        private readonly FlashcardDeckReadMapper $mapper,
        private readonly FlashcardReadMapper $flashcard_mapper,
    ) {}

    public function findRatingStats(FlashcardDeckId $id, Language $front_lang, Language $back_lang): RatingStatsReadCollection
    {
        return $this->flashcard_mapper->findFlashcardStats($front_lang, $back_lang, $id, null);
    }

    public function findDetails(UserId $user_id, FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        return $this->mapper->findDetails($user_id, $id, $search, $page, $per_page);
    }

    /** @return OwnerCategoryRead[] */
    public function getByUser(UserId $user_id, Language $front_lang, Language $back_lang, ?string $search, int $page, int $per_page): array
    {
        return $this->mapper->getByUser($user_id, $front_lang, $back_lang, $search, $page, $per_page);
    }

    /** @return OwnerCategoryRead[] */
    public function getAdminDecks(UserId $user_id, Language $front_lang, Language $back_lang, ?LanguageLevel $level, ?string $search, int $page, int $per_page): array
    {
        return $this->mapper->getAdminDecks($user_id, $front_lang, $back_lang, $level, $search, $page, $per_page);
    }
}
