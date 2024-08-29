<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\UserId;

interface IFlashcardSelector
{
    /** @return Flashcard[] */
    public function select(UserId $user_id, CategoryId $category_id, int $limit): array;
}