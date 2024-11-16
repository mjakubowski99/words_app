<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Psr\Log\LoggerInterface;
use Flashcard\Domain\Models\Owner;
use Shared\Database\ITransactionManager;
use Shared\Exceptions\UnauthorizedException;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class MergeFlashcardCategoriesHandler
{
    public function __construct(
        private IFlashcardRepository $repository,
        private IFlashcardCategoryRepository $category_repository,
        private ITransactionManager $transaction_manager,
        private LoggerInterface $logger,
    ) {}

    public function handle(Owner $owner, CategoryId $from_category_id, CategoryId $to_category_id, ?string $new_name = null): bool
    {
        $this->authorize($owner, $from_category_id, $to_category_id);

        $this->transaction_manager->beginTransaction();

        try {
            $this->repository->replaceCategory($from_category_id, $to_category_id);

            $this->repository->replaceInSessions($from_category_id, $to_category_id);

            $category = $this->category_repository->findById($from_category_id);

            $this->category_repository->removeCategory($category);

            if ($new_name) {
                $category = $this->category_repository->findById($to_category_id);
                $category->setName($new_name);

                $this->category_repository->updateCategory($category);
            }

            $this->transaction_manager->commit();

            return true;
        } catch (\Throwable $exception) {
            $this->transaction_manager->rollback();

            $this->logger->error('Failed to merge flashcards', [
                'from_category_id' => $from_category_id,
                'to_category_id' => $to_category_id,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function authorize(Owner $owner, CategoryId $from_category_id, CategoryId $to_category_id): void
    {
        if ($from_category_id->getValue() === $to_category_id->getValue()) {
            throw new UnauthorizedException('Cannot merge two same categories!');
        }

        $from_category = $this->category_repository->findById($from_category_id);
        $to_category = $this->category_repository->findById($to_category_id);

        if (!$from_category->getOwner()->equals($owner)) {
            throw new UnauthorizedException('You are not category owner!');
        }
        if (!$to_category->getOwner()->equals($owner)) {
            throw new UnauthorizedException('You are not category owner!');
        }
    }
}
