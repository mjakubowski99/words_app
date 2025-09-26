<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\Language;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

interface IFlashcardReadRepository
{
    public function findStatsByUser(UserId $user_id, Language $front_lang, Language $back_lang, ?FlashcardOwnerType $owner_type): RatingStatsReadCollection;

    public function findByUser(UserId $user_id, Language $front_lang, Language $back_lang, ?string $search, int $page, int $per_page): UserFlashcardsRead;
}
