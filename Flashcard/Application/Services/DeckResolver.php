<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class DeckResolver
{
    public function __construct(
        private IFlashcardDeckRepository $repository
    ) {}

    public function resolveByName(UserId $user_id, Language $front_lang, Language $back_lang, string $name, LanguageLevel $level): ResolvedDeck
    {
        $existing_deck = $this->repository->searchByName($user_id, $name, $front_lang, $back_lang);

        if ($existing_deck) {
            return new ResolvedDeck(true, $existing_deck);
        }

        $deck = new Deck(
            Owner::fromUser($user_id),
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
