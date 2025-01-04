<?php

declare(strict_types=1);

namespace Flashcard\Application\Facades;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Shared\Flashcard\IFlashcardAdminFacade;
use Shared\Exceptions\UnauthorizedException;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Command\ImportFlashcardsHandler;
use Flashcard\Application\Repository\IFlashcardRepository;

class FlashcardAdminFacade implements IFlashcardAdminFacade
{
    public function __construct(
        private IFlashcardRepository $repository,
        private ImportFlashcardsHandler $handler,
    ) {}

    public function importDeck(string $admin_id, string $deck_name, LanguageLevel $level, array $flashcard_rows): void
    {
        $owner = new Owner(new OwnerId($admin_id), FlashcardOwnerType::ADMIN);

        $this->handler->handle($owner, $deck_name, $level, $flashcard_rows);
    }

    public function update(
        int $flashcard_id,
        string $front_word,
        string $front_context,
        string $back_word,
        string $back_context
    ): void {
        $flashcard_id = new FlashcardId($flashcard_id);

        $flashcard = $this->repository->find($flashcard_id);

        $flashcard->setFrontWord($front_word);
        $flashcard->setBackWord($back_word);
        $flashcard->setFrontContext($front_context);
        $flashcard->setBackContext($back_context);

        $this->repository->update($flashcard);
    }

    public function delete(int $flashcard_id): void
    {
        $flashcard_id = new FlashcardId($flashcard_id);

        if ($this->repository->hasAnySessions($flashcard_id)) {
            throw new UnauthorizedException('Flashcard is in usage');
        }

        $this->repository->delete($flashcard_id);
    }
}
