<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Carbon\Carbon;
use Shared\Enum\LanguageLevel;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;

class FlashcardDeckReadMapper
{
    public function __construct(
        private readonly DB $db,
        private readonly FlashcardReadMapper $flashcard_mapper,
    ) {}

    public function findDetails(FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        $deck = $this->findDeck($id);

        if (!$deck) {
            throw new ModelNotFoundException('Category not found');
        }

        $flashcards = $this->flashcard_mapper->search($id, null, $search, $page, $per_page);

        $flashcards_count = $this->db::table('flashcards')
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->count();

        return new DeckDetailsRead(
            new FlashcardDeckId($deck->id),
            $deck->name,
            $flashcards,
            $page,
            $per_page,
            $flashcards_count
        );
    }

    public function getByUser(UserId $user_id, ?string $search, int $page, int $per_page): array
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
            ->where('flashcard_decks.user_id', $user_id->getValue())
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(DB::raw('LOWER(flashcard_decks.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
            })
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
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
            ->orderByRaw('flashcard_decks.created_at DESC NULLS LAST')
            ->get()
            ->map(function (object $data) {
                return new OwnerCategoryRead(
                    new FlashcardDeckId($data->id),
                    $data->name,
                    LanguageLevel::from($data->most_frequent_language_level ?? $data->default_language_level),
                    $data->flashcards_count,
                    (float) $data->avg_rating * 100.0,
                    $data->last_learnt_at ? Carbon::parse($data->last_learnt_at) : null,
                );
            })->toArray();
    }

    private function findDeck(FlashcardDeckId $id): ?object
    {
        return $this->db::table('flashcard_decks')
            ->find($id->getValue());
    }
}
