<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Carbon\Carbon;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\GeneralRating;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\RatingStatsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

class FlashcardDeckReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findDeckStats(FlashcardDeckId $id): RatingStatsReadCollection
    {
        $results = $this->db::table('flashcards')
            ->where('flashcard_deck_id', '=', $id->getValue())
            ->leftJoin(
                'learning_session_flashcards',
                'learning_session_flashcards.flashcard_id',
                '=',
                'flashcards.id'
            )
            ->whereNotNull('rating')
            ->groupBy('rating')
            ->select('rating', DB::raw('COUNT(rating) as rating_count'))
            ->get()
            ->all();

        $ratings_count = array_sum(array_map(fn (object $result) => $result->rating_count, $results));

        $data = [];

        foreach (Rating::cases() as $rating) {
            $result = array_values(array_filter($results, fn ($result) => $result->rating === $rating->value));
            $result = isset($result[0]) ? $result[0]->rating_count : 0.0;

            $data[] = new RatingStatsRead(
                new GeneralRating($rating->value),
                $ratings_count === 0 ? 0.0 : (float) $result / $ratings_count * 100
            );
        }

        return new RatingStatsReadCollection($data);
    }

    public function findDetails(FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        $rating = Rating::maxRating();

        $deck = $this->db::table('flashcard_decks')->find($id->getValue());

        if (!$deck) {
            throw new ModelNotFoundException('Category not found');
        }

        $results = $this->db::table('flashcards')
            ->latest()
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $search = mb_strtolower($search);

                    return $q->where(DB::raw('LOWER(flashcards.front_word)'), 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('LOWER(flashcards.back_word)'), 'LIKE', '%' . $search . '%');
                });
            })
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->selectRaw("
                flashcards.*,
                (SELECT learning_session_flashcards.rating
                 FROM learning_session_flashcards 
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                 ORDER BY learning_session_flashcards.updated_at DESC
                 LIMIT 1
                ) as last_rating,
                (SELECT AVG(COALESCE(rating,0)/{$rating}::float)
                 FROM learning_session_flashcards 
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                ) as rating_ratio
            ")
            ->get()
            ->map(function (object $data) {
                return new FlashcardRead(
                    new FlashcardId($data->id),
                    $data->front_word,
                    Language::from($data->front_lang),
                    $data->back_word,
                    Language::from($data->back_lang),
                    $data->front_context,
                    $data->back_context,
                    new GeneralRating($data->last_rating),
                    LanguageLevel::from($data->language_level),
                    (float) $data->rating_ratio
                );
            })->toArray();

        $flashcards_count = $this->db::table('flashcards')
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->count();

        return new DeckDetailsRead(
            new FlashcardDeckId($deck->id),
            $deck->name,
            $results,
            $page,
            $per_page,
            $flashcards_count
        );
    }

    public function getByOwner(Owner $owner, ?string $search, int $page, int $per_page): array
    {
        $rating = Rating::maxRating();

        $flashcard_stats = $this->db::table('learning_session_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->whereColumn('flashcards.flashcard_deck_id', 'flashcard_decks.id')
            ->whereNotNull('learning_session_flashcards.rating')
            ->limit(1)
            ->selectRaw("
                MAX(learning_session_flashcards.updated_at) as last_learnt_at,
                AVG(COALESCE(rating,0)/{$rating}::float) as avg_rating
            ");

        return $this->db::table('flashcard_decks')
            ->where('flashcard_decks.user_id', $owner->getId())
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(DB::raw('LOWER(flashcard_decks.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
            })
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->latest()
            ->leftJoinLateral($flashcard_stats, 'flashcard_stats')
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
                'flashcard_stats.avg_rating'
            )
            ->get()
            ->map(function (object $data) {
                return new OwnerCategoryRead(
                    new FlashcardDeckId($data->id),
                    $data->name,
                    LanguageLevel::from($data->most_frequent_language_level ?? $data->default_language_level),
                    $data->flashcards_count,
                    (float) $data->avg_rating,
                    $data->last_learnt_at ? Carbon::parse($data->last_learnt_at) : null,
                );
            })->toArray();
    }
}
