<?php

declare(strict_types=1);

namespace User\Application\Command;

use Psr\Log\LoggerInterface;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Database\ITransactionManager;
use User\Application\Repositories\IUserRepository;
use User\Application\Repositories\IReportRepository;

class DeleteUserHandler
{
    public function __construct(
        private ITransactionManager $transaction_manager,
        private IFlashcardFacade $flashcard_facade,
        private IUserRepository $user_repository,
        private IReportRepository $ticket_repository,
        private LoggerInterface $logger,
    ) {}

    public function delete(UserId $user_id): bool
    {
        $this->transaction_manager->beginTransaction();

        try {
            $this->flashcard_facade->deleteUserData($user_id);

            $this->ticket_repository->detachFromUser($user_id);

            $this->user_repository->delete($user_id);

            $this->transaction_manager->commit();

            return true;
        } catch (\Throwable $exception) {
            $this->transaction_manager->rollback();
            $this->logger->error('Failed to delete user', [
                'user_id' => $user_id->getValue(),
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
