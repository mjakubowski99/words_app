<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardCategory;

interface IFlashcardSelector
{
    /** @return Flashcard[] */
    public function select(UserId $user_id, FlashcardCategory $category, int $limit): array;
}
