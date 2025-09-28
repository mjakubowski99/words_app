<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Application\Services\DeckResolver;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Services\FlashcardGeneratorService;

class RegenerateAdditionalFlashcardsHandler
{
    public function __construct(
        private DeckResolver $deck_resolver,
        private FlashcardGeneratorService $flashcard_generator_service,
    ) {}

    public function handle(Owner $owner, Language $front, Language $back, FlashcardDeckId $id, int $words_count, int $words_count_to_save): void
    {
        $resolved_deck = $this->deck_resolver->resolveById($id);

        if (!$resolved_deck->getDeck()->getOwner()->equals($owner)) {
            throw new ForbiddenException('You must be deck owner');
        }

        $this->flashcard_generator_service->generate(
            $resolved_deck,
            $front,
            $back,
            $resolved_deck->getDeck()->getName(),
            $words_count,
            $words_count_to_save
        );
    }
}
