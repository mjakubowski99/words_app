<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Shared\Database\ITransactionManager;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Services\FlashcardDuplicateService;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class ImportFlashcardsHandler
{
    public function __construct(
        private IFlashcardDeckRepository $deck_repository,
        private IFlashcardRepository $flashcard_repository,
        private FlashcardDuplicateService $duplicate_service,
        private ITransactionManager $manager,
    ) {}

    public function handle(Owner $owner, string $deck_name, LanguageLevel $level, array $rows): void
    {
        $this->manager->beginTransaction();

        try {
            $deck = $this->resolveDeck($owner, $deck_name, $level);

            $flashcards = [];

            foreach ($rows as $row) {
                $flashcards[] = $this->buildFlashcardModel($owner, $deck, $row);
            }

            $flashcards = $this->duplicate_service->removeDuplicates($deck, $flashcards);

            $this->flashcard_repository->createMany($flashcards);

            $this->manager->commit();
        } catch (\Throwable $exception) {
            $this->manager->rollback();

            throw $exception;
        }
    }

    private function resolveDeck(Owner $owner, string $deck_name, LanguageLevel $level): Deck
    {
        $deck = $this->deck_repository->searchByNameAdmin($deck_name);

        if (!$deck) {
            $deck = new Deck(
                $owner,
                $deck_name,
                $deck_name,
                $level
            );

            return $this->deck_repository->create($deck);
        }

        return $deck;
    }

    private function buildFlashcardModel(Owner $owner, Deck $deck, array $row): Flashcard
    {
        return new Flashcard(
            FlashcardId::noId(),
            $row['front_word'],
            Language::pl(),
            $row['back_word'],
            Language::en(),
            $row['front_context'],
            $row['back_context'],
            $owner,
            $deck,
            $deck->getDefaultLanguageLevel(),
            array_key_exists('emoji', $row) ? new Emoji($row['emoji']) : null,
        );
    }
}
