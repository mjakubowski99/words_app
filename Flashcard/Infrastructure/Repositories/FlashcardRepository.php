<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Infrastructure\Mappers\FlashcardMapper;
use Flashcard\Domain\Repositories\IFlashcardRepository;

class FlashcardRepository implements IFlashcardRepository
{
    public function __construct(private FlashcardMapper $mapper) {}

    public function getRandomFlashcards(UserId $user_id, int $limit): array
    {
        return $this->mapper->getRandomFlashcards($user_id, $limit);
    }

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit): array
    {
        return $this->mapper->getRandomFlashcardsByCategory($id, $limit);
    }
}
