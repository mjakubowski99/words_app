<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Carbon\Carbon;
use Shared\Enum\LanguageLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class FlashcardDeckReadMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
        private readonly FlashcardReadMapper $flashcard_mapper,
    ) {}

    public function findDetails(UserId $user_id, FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        $deck = $this->findDeck($id, $user_id);

        if (!$deck) {
            throw new ModelNotFoundException('Category not found');
        }

        $flashcards = $this->flashcard_mapper->search($user_id, $id, null, $search, $page, $per_page);

        $flashcards_count = $this->db::table('flashcards')
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->count();

        $total_avg_rating = $this->getRatingStats(collect([$id->getValue()]), $user_id)->first()->total_avg_rating ?? 0.0;

        $avg_rating = $this->calculateAvgRating((float) $total_avg_rating, $flashcards_count);

        return new DeckDetailsRead(
            new FlashcardDeckId($deck->id),
            $deck->name,
            $flashcards,
            $page,
            $per_page,
            $flashcards_count,
            $this->buildOwner($deck->user_id, $deck->admin_id)->getOwnerType(),
            LanguageLevel::from($deck->most_frequent_language_level ?? $deck->default_language_level),
            $deck->last_learnt_at ? Carbon::parse($deck->last_learnt_at) : null,
            $avg_rating,
        );
    }

    /** @return OwnerCategoryRead[] */
    public function getAdminDecks(UserId $user_id, ?LanguageLevel $level, ?string $search, int $page, int $per_page): array
    {
        $flashcard_stats = $this->db::table('learning_session_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->join('learning_sessions', 'learning_sessions.id', '=', 'learning_session_flashcards.learning_session_id')
            ->whereColumn('flashcards.flashcard_deck_id', 'flashcard_decks.id')
            ->whereNotNull('learning_session_flashcards.rating')
            ->where('learning_sessions.user_id', $user_id->getValue())
            ->limit(1)
            ->selectRaw('
                MAX(learning_session_flashcards.updated_at) as last_learnt_at,
                SUM(COALESCE(rating,0)) as rating_sum
            ');

        $activities = $this->db::table('flashcard_deck_activities')->where('user_id', $user_id);

        $results = $this->db::table('flashcard_decks')
            ->when($level !== null, fn ($q) => $q->where('flashcard_decks.default_language_level', '=', $level->value))
            ->whereNotNull('flashcard_decks.admin_id')
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(DB::raw('LOWER(flashcard_decks.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
            })
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->leftJoinLateral($flashcard_stats, 'flashcard_stats')
            ->leftJoinSub($activities, 'flashcard_deck_activities', function ($join) {
                $join->on('flashcard_deck_activities.flashcard_deck_id', '=', 'flashcard_decks.id');
            })
            ->select(
                'flashcard_decks.*',
                DB::raw('(SELECT language_level
                    FROM flashcards
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    GROUP BY language_level
                    ORDER BY COUNT(*) DESC
                LIMIT 1) as most_frequent_language_level'),
                DB::raw('(
                    SELECT COUNT(flashcards.id)
                    FROM flashcards
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    ) as flashcards_count'),
                'flashcard_stats.last_learnt_at',
                'flashcard_stats.rating_sum',
            )
            ->orderByRaw('
                flashcard_deck_activities.last_viewed_at DESC NULLS LAST,
                flashcard_decks.name ASC
            ')
            ->get();

        $rating_stats = $this->getRatingStats($results->pluck('id'), $user_id);

        return $results->map(function (object $data) use ($rating_stats) {
            $rating_sum = $rating_stats->where('flashcard_deck_id', $data->id)->first()->total_avg_rating ?? 0.0;

            $avg_rating = $this->calculateAvgRating((float) $rating_sum, $data->flashcards_count);

            return new OwnerCategoryRead(
                new FlashcardDeckId($data->id),
                $data->name,
                LanguageLevel::from($data->most_frequent_language_level ?? $data->default_language_level),
                $data->flashcards_count,
                $avg_rating,
                $data->last_learnt_at ? Carbon::parse($data->last_learnt_at) : null,
                FlashcardOwnerType::ADMIN,
            );
        })->toArray();
    }

    /** @return OwnerCategoryRead[] */
    public function getByUser(UserId $user_id, ?string $search, int $page, int $per_page): array
    {
        $activities = $this->db::table('flashcard_deck_activities')->where('user_id', $user_id);

        $results = $this->db::table('flashcard_decks')
            ->where('flashcard_decks.user_id', $user_id->getValue())
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(DB::raw('LOWER(flashcard_decks.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
            })
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->leftJoinSub($activities, 'flashcard_deck_activities', function ($join) {
                $join->on('flashcard_deck_activities.flashcard_deck_id', '=', 'flashcard_decks.id');
            })
            ->select(
                'flashcard_decks.*',
                DB::raw('(SELECT language_level
                    FROM flashcards
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    GROUP BY language_level
                    ORDER BY COUNT(*) DESC
                LIMIT 1) as most_frequent_language_level'),
                DB::raw('(
                    SELECT COUNT(flashcards.id)
                    FROM flashcards
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    ) as flashcards_count'),
                DB::raw("(
                    SELECT MAX(lsf.updated_at)
                    FROM learning_session_flashcards as lsf
                    LEFT JOIN flashcards ON lsf.flashcard_id = flashcards.id
                    LEFT JOIN learning_sessions as ls on ls.id = lsf.learning_session_id
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    AND ls.user_id = '{$user_id->getValue()}'
                    ) as last_learnt_at"),
            )
            ->orderByRaw('
                CASE
                    WHEN flashcard_deck_activities.last_viewed_at IS NOT NULL THEN flashcard_deck_activities.last_viewed_at
                    ELSE flashcard_decks.created_at
                END DESC NULLS LAST
            ')
            ->get();

        $rating_stats = $this->getRatingStats($results->pluck('id'), $user_id);

        return $results->map(function (object $data) use ($rating_stats) {
            $rating_sum = $rating_stats->where('flashcard_deck_id', $data->id)->first()->total_avg_rating ?? 0.0;

            $avg_rating = $this->calculateAvgRating((float) $rating_sum, $data->flashcards_count);

            return new OwnerCategoryRead(
                new FlashcardDeckId($data->id),
                $data->name,
                LanguageLevel::from($data->most_frequent_language_level ?? $data->default_language_level),
                $data->flashcards_count,
                $avg_rating,
                $data->last_learnt_at ? Carbon::parse($data->last_learnt_at) : null,
                FlashcardOwnerType::USER,
            );
        })->toArray();
    }

    private function findDeck(FlashcardDeckId $id, UserId $user_id): ?object
    {
        return $this->db::table('flashcard_decks')
            ->select([
                'flashcard_decks.*',
                DB::raw('(SELECT language_level
                    FROM flashcards
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    GROUP BY language_level
                    ORDER BY COUNT(*) DESC
                LIMIT 1) as most_frequent_language_level'),
                DB::raw("(
                    SELECT MAX(lsf.updated_at)
                    FROM learning_session_flashcards as lsf
                    LEFT JOIN flashcards ON lsf.flashcard_id = flashcards.id
                    LEFT JOIN learning_sessions as ls on ls.id = lsf.learning_session_id
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    AND ls.user_id = '{$user_id->getValue()}'
                    ) as last_learnt_at"),
            ])
            ->find($id->getValue());
    }

    private function getRatingStats(Collection $ids, UserId $user_id): Collection
    {
        $flashcard_avg_ratings = DB::table('learning_session_flashcards as lsf1')
            ->leftJoin('learning_sessions', 'lsf1.learning_session_id', '=', 'learning_sessions.id')
            ->selectRaw('
                lsf1.flashcard_id,
                AVG(lsf1.rating)::float as avg_rating
            ')
            ->whereNotNull('lsf1.rating')
            ->where('learning_sessions.user_id', $user_id->getValue())
            ->whereIn('lsf1.id', function ($query) {
                $query->select('id')
                    ->from('learning_session_flashcards as lsf2')
                    ->whereColumn('lsf2.flashcard_id', 'lsf1.flashcard_id')
                    ->orderByDesc('lsf2.id')
                    ->limit(2);
            })
            ->groupBy('lsf1.flashcard_id');

        return DB::table('flashcards')
            ->joinSub($flashcard_avg_ratings, 'avg_ratings', function ($join) {
                $join->on('avg_ratings.flashcard_id', '=', 'flashcards.id');
            })
            ->whereIn('flashcards.flashcard_deck_id', $ids)
            ->selectRaw('flashcards.flashcard_deck_id, SUM(avg_ratings.avg_rating) as total_avg_rating')
            ->groupBy('flashcards.flashcard_deck_id')
            ->get();
    }

    private function calculateAvgRating(float $total_avg_rating, int $flashcards_count): float
    {
        return $flashcards_count ? $total_avg_rating / ($flashcards_count * Rating::maxRating()) * 100.0 : 0;
    }
}
