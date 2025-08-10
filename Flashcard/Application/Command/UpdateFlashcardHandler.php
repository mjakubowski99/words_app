<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Models\Emoji;
use Flashcard\Domain\Models\Flashcard;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Application\Repository\IStoryRepository;
use Flashcard\Application\Repository\IFlashcardRepository;

class UpdateFlashcardHandler
{
    public function __construct(
        private IFlashcardRepository $repository,
        private IStoryRepository $story_repository,
    ) {}

    public function handle(UpdateFlashcard $command): void
    {
        $flashcard = $this->repository->find($command->getId());

        if (!$flashcard->getOwner()->equals($command->getOwner())) {
            throw new ForbiddenException('You must be flashcard owner to update flashcard');
        }

        $before_hash = $flashcard->hash();

        $flashcard = new Flashcard(
            $command->getId(),
            $command->getFrontWord(),
            $command->getFrontLang(),
            $command->getBackWord(),
            $command->getBackLang(),
            $command->getFrontContext(),
            $command->getBackContext(),
            $command->getOwner(),
            $flashcard->getDeck(),
            $command->getLanguageLevel(),
            $command->getEmoji() ? new Emoji($command->getEmoji()) : null,
        );

        $after_hash = $flashcard->hash();

        if ($before_hash !== $after_hash) {
            $story_ids = $this->repository->getStoryIdForFlashcards([$flashcard->getId()]);
            if (count($story_ids) > 0) {
                $this->story_repository->bulkDelete($story_ids);
            }
        }

        $this->repository->update($flashcard);
    }
}
