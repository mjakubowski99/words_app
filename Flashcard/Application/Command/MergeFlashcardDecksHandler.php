<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Psr\Log\LoggerInterface;
use Flashcard\Domain\Models\Owner;
use Shared\Database\ITransactionManager;
use Shared\Exceptions\UnauthorizedException;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class MergeFlashcardDecksHandler
{
    public function __construct(
        private IFlashcardRepository $repository,
        private IFlashcardDeckRepository $deck_repository,
        private ITransactionManager $transaction_manager,
        private LoggerInterface $logger,
    ) {}

    public function handle(Owner $owner, FlashcardDeckId $from_deck_id, FlashcardDeckId $to_deck_id, ?string $new_name = null): bool
    {
        $this->authorize($owner, $from_deck_id, $to_deck_id);

        $this->transaction_manager->beginTransaction();

        try {
            $this->repository->replaceDeck($from_deck_id, $to_deck_id);

            $this->repository->replaceInSessions($from_deck_id, $to_deck_id);

            $deck = $this->deck_repository->findById($from_deck_id);

            $this->deck_repository->remove($deck);

            if ($new_name) {
                $deck = $this->deck_repository->findById($to_deck_id);
                $deck->setName($new_name);

                $this->deck_repository->update($deck);
            }

            $this->transaction_manager->commit();

            return true;
        } catch (\Throwable $exception) {
            $this->transaction_manager->rollback();

            $this->logger->error('Failed to merge flashcards', [
                'from_deck_id' => $from_deck_id,
                'to_deck_id' => $to_deck_id,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function authorize(Owner $owner, FlashcardDeckId $from_deck_id, FlashcardDeckId $to_deck_id): void
    {
        if ($from_deck_id->getValue() === $to_deck_id->getValue()) {
            throw new UnauthorizedException('Cannot merge two same decks!');
        }

        $from_deck = $this->deck_repository->findById($from_deck_id);
        $to_deck = $this->deck_repository->findById($to_deck_id);

        if (!$from_deck->getOwner()->equals($owner)) {
            throw new UnauthorizedException('You are not deck owner!');
        }
        if (!$to_deck->getOwner()->equals($owner)) {
            throw new UnauthorizedException('You are not deck owner!');
        }
    }
}
