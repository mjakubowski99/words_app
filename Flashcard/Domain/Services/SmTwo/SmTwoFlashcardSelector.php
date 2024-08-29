<?php

namespace Flashcard\Domain\Services\SmTwo;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Domain\Services\IFlashcardSelector;
use Shared\Utils\ValueObjects\UserId;

class SmTwoFlashcardSelector implements IFlashcardSelector
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository
    ) {}

    public function select(UserId $user_id, CategoryId $category_id, int $limit): array
    {
        return $this->repository->getFlashcardsWithLowestRepetitionInterval($user_id, $category_id, $limit);
    }
}