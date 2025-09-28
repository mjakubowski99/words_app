<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Models\Emoji;
use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Illuminate\Database\Query\Builder;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;
use Flashcard\Infrastructure\SortCriteria\Postgres\PostgresSortCriteria;

class FlashcardFromSmTwoMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db
    ) {}

    public function getNextFlashcards(
        UserId $user_id,
        int $limit,
        array $exclude_flashcard_ids,
        array $sort_criteria,
        int $cards_per_session,
        bool $from_poll,
        bool $exclude_from_poll,
        Language $front,
        Language $back,
    ): array {
        $sort_sql = array_map(fn (PostgresSortCriteria $criteria) => $criteria->apply(), $sort_criteria);

        $flashcard_limit = max(3, (int) (0.1 * $cards_per_session));

        if ($from_poll) {
            $query = $this->db::table('flashcards_poll')
                ->where('flashcards_poll.user_id', $user_id)
                ->leftJoin('flashcards', 'flashcards.id', '=', 'flashcards_poll.flashcard_id');
        } else {
            $query = $this->db::table('flashcards');
        }

        return $query
            ->where('flashcards.front_lang', $front->value)
            ->where('flashcards.back_lang', $back->value)
            ->when($exclude_from_poll, fn ($q) => $q->whereNotIn(
                'flashcards.id',
                fn ($q) => $q->select('flashcard_id')->from('flashcard_poll_items')->where('user_id', $user_id)
            ))
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->where(function (Builder $builder) use ($user_id) {
                return $builder->where('flashcards.user_id', $user_id->getValue())
                    ->orWhere('sm_two_flashcards.user_id', $user_id->getValue());
            })
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->take($limit)
            ->orderByRaw("
                CASE WHEN COALESCE(sm_two_flashcards.repetitions_in_session, 0) < {$flashcard_limit} then 1 else 0 end DESC,
            " . implode(',', $sort_sql))
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
                'sm_two_flashcards.last_rating',
            )
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    public function getNextFlashcardsByDeck(UserId $user_id, FlashcardDeckId $deck_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria, int $cards_per_session, bool $from_poll, Language $front, Language $back): array
    {
        $sort_sql = array_map(fn (PostgresSortCriteria $criteria) => $criteria->apply(), $sort_criteria);

        $flashcard_limit = max(3, (int) (0.1 * $cards_per_session));

        if ($from_poll) {
            $query = $this->db::table('flashcard_poll_items')
                ->where('flashcard_poll_items.user_id', $user_id)
                ->leftJoin('flashcards', 'flashcards.id', '=', 'flashcard_poll_items.flashcard_id');
        } else {
            $query = $this->db::table('flashcards');
        }

        return $query
            ->where('flashcards.front_lang', $front->value)
            ->where('flashcards.back_lang', $back->value)
            ->whereNotIn('flashcards.id', array_map(fn (FlashcardId $id) => $id->getValue(), $exclude_flashcard_ids))
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->where('flashcards.flashcard_deck_id', $deck_id->getValue())
            ->leftJoin('sm_two_flashcards', function ($join) use ($user_id) {
                $join->on('sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
                    ->on('sm_two_flashcards.user_id', '=', DB::raw("'{$user_id}'"));
            })
            ->take($limit)
            ->orderByRaw("
                CASE WHEN COALESCE(sm_two_flashcards.repetitions_in_session, 0) < {$flashcard_limit} then 1 else 0 end DESC,
            " . implode(',', $sort_sql))
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
                'sm_two_flashcards.last_rating',
            )
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    private function map(object $data): Flashcard
    {
        $deck = $data->flashcard_deck_id ? (new Deck(
            $this->buildOwner((string) $data->deck_user_id, (string) $data->deck_admin_id),
            $data->deck_tag,
            $data->deck_name,
            LanguageLevel::from($data->deck_default_language_level)
        ))->init(new FlashcardDeckId($data->flashcard_deck_id)) : null;

        return new Flashcard(
            new FlashcardId($data->id),
            $data->front_word,
            \Shared\Utils\ValueObjects\Language::from($data->front_lang),
            $data->back_word,
            \Shared\Utils\ValueObjects\Language::from($data->back_lang),
            $data->front_context,
            $data->back_context,
            $this->buildOwner((string) $data->user_id, (string) $data->admin_id),
            $deck,
            LanguageLevel::from($data->language_level),
            $data->emoji ? Emoji::fromUnicode($data->emoji) : null,
            $data->last_rating ? Rating::from($data->last_rating) : null,
        );
    }
}
