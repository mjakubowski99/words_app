<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;

interface IFlashcardRepository
{
    public function getRandomFlashcards(UserId $user_id, int $limit): array;

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit): array;
}
