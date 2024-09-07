<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardCategory;
use Flashcard\Application\DTO\UserFlashcardCategoryDTO;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;

class GetUserCategories
{
    public function __construct(private IFlashcardCategoryRepository $repository) {}

    /** @return UserFlashcardCategoryDTO[] */
    public function handle(UserId $user_id, int $page, int $per_page): array
    {
        return array_map(function (FlashcardCategory $category) {
            return new UserFlashcardCategoryDTO($category->getId(), $category->getName());
        }, $this->repository->getByUser($user_id, $page, $per_page));
    }
}
