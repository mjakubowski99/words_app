<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class FlashcardFromSmTwoMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function getNextFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids, bool $get_oldest): array
    {
        $random = round(lcg_value(), 2);

        return $this->db::table('flashcards')
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->where('flashcards.user_id', $owner->getId())
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw('
                CASE 
                    WHEN sm_two_flashcards.updated_at IS NOT NULL AND sm_two_flashcards.repetition_interval IS NOT NULL 
                         AND DATE(sm_two_flashcards.updated_at) + CAST(sm_two_flashcards.repetition_interval AS INTEGER) <= CURRENT_DATE
                    THEN 1
                    ELSE 0
                END DESC,' .
                ($get_oldest ? 'CASE WHEN COALESCE(repetition_interval, 1.0) > 1.0 THEN 1 ELSE 0 END ASC,' : '') .
                ($get_oldest ? 'sm_two_flashcards.updated_at ASC NULLS FIRST,' : '')
            . "COALESCE(repetition_interval, 1.0) ASC,
                CASE 
                    WHEN repetition_interval IS NOT NULL AND {$random} < 0.7 THEN 1
                ELSE 0
                END DESC,
                sm_two_flashcards.updated_at ASC NULLS FIRST
            ")
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

    public function getNextFlashcardsByDeck(FlashcardDeckId $deck_id, int $limit, array $exclude_flashcard_ids, bool $get_oldest): array
    {
        return $this->db::table('flashcards')
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->where('flashcards.flashcard_deck_id', $deck_id->getValue())
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw(
                '
                CASE 
                    WHEN repetition_interval IS NULL THEN 1
                    ELSE 0
                END DESC,
                CASE 
                    WHEN sm_two_flashcards.updated_at IS NOT NULL AND sm_two_flashcards.repetition_interval IS NOT NULL 
                         AND DATE(sm_two_flashcards.updated_at) + CAST(sm_two_flashcards.repetition_interval AS INTEGER) <= CURRENT_DATE
                    THEN 1
                    ELSE 0
                END DESC,' .
                ($get_oldest ? 'CASE WHEN COALESCE(repetition_interval, 1.0) > 1.0 THEN 1 ELSE 0 END ASC,' : '') .
                ($get_oldest ? 'sm_two_flashcards.updated_at ASC NULLS FIRST,' : '')
                . 'COALESCE(repetition_interval, 1.0) ASC,
                sm_two_flashcards.updated_at ASC NULLS FIRST'
            )
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
