<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class DeckResolver
{
    public function __construct(
        private IFlashcardDeckRepository $repository
    ) {}

    public function resolveByName(Owner $owner, string $name, LanguageLevel $level): ResolvedDeck
    {
        $existing_deck = $this->repository->searchByName($owner, $name);

        if ($existing_deck) {
            return new ResolvedDeck(true, $existing_deck);
        }

        $deck = new Deck(
            $owner,
            mb_strtolower($name),
            $name,
            $level,
        );
        $deck = $this->repository->create($deck);

        return new ResolvedDeck(false, $deck);
    }

    public function resolveById(FlashcardDeckId $id): ResolvedDeck
    {
        $deck = $this->repository->findById($id);

        return new ResolvedDeck(true, $deck);
    }
}
