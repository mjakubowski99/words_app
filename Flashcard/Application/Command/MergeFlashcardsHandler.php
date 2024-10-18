<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Database\ITransactionManager;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class MergeFlashcardsHandler
{
    public function __construct(
        private IFlashcardRepository $repository,
        private IFlashcardCategoryRepository $category_repository,
        private ITransactionManager $transaction_manager,
    ) {}

    public function handle(CategoryId $from_category_id, CategoryId $to_category_id): void
    {
        $this->transaction_manager->beginTransaction();

        try {
            $this->repository->replaceCategory($from_category_id, $to_category_id);

            $this->repository->replaceInSessions($from_category_id, $to_category_id);

            $category = $this->category_repository->findById($from_category_id);

            $this->category_repository->removeCategory($category);

            $this->transaction_manager->commit();
        } catch (\Throwable $exception) {
            $this->transaction_manager->rollback();

            throw $exception;
        }
    }
}
