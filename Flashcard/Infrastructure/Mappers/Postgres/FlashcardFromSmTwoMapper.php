<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Infrastructure\SortCriteria\Postgres\PostgresSortCriteria;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\Language;

class FlashcardFromSmTwoMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function getNextFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids, array $sort_criteria): array
    {
        $sort_sql = array_map(fn (PostgresSortCriteria $criteria) => $criteria->apply(), $sort_criteria);

        return $this->db::table('flashcards')
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->where('flashcards.user_id', $owner->getId())
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw(implode(',', $sort_sql))
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
            )
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    public function getNextFlashcardsByDeck(FlashcardDeckId $deck_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria): array
    {
        $sort_sql = array_map(fn (PostgresSortCriteria $criteria) => $criteria->apply(), $sort_criteria);

        return $this->db::table('flashcards')
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->where('flashcards.flashcard_deck_id', $deck_id->getValue())
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw(implode(',', $sort_sql))
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
            )
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    private function map(object $data): Flashcard
    {
        $deck = $data->flashcard_deck_id ? (new Deck(
            new Owner(new OwnerId($data->deck_user_id), FlashcardOwnerType::USER),
            $data->deck_tag,
            $data->deck_name,
        ))->init(new FlashcardDeckId($data->flashcard_deck_id)) : null;

        return new Flashcard(
            new FlashcardId($data->id),
            $data->front_word,
            Language::from($data->front_lang),
            $data->back_word,
            Language::from($data->back_lang),
            $data->front_context,
            $data->back_context,
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $deck,
        );
    }
}
