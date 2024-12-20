<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\GeneralRating;
use Flashcard\Application\ReadModels\RatingStatsRead;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

class FlashcardReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findFlashcardStats(?FlashcardDeckId $deck_id, ?Owner $owner): RatingStatsReadCollection
    {
        $results = $this->db::table('flashcards')
            ->when($deck_id !== null, fn ($q) => $q->where('flashcard_deck_id', '=', $deck_id->getValue()))
            ->when($owner !== null, fn ($q) => $q->where('user_id', '=', $owner->getId()->getValue()))
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

    public function getByUser(Owner $owner, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        $user_flashcards = $this->search(null, $owner, $search, $page, $per_page);

        $count = $this->db::table('flashcards')
            ->where('user_id', $owner->getId())
            ->count();

        return new UserFlashcardsRead(
            $owner,
            $user_flashcards,
            $page,
            $per_page,
            $count
        );
    }

    public function search(?FlashcardDeckId $deck_id, ?Owner $owner, ?string $search, int $page, int $per_page): array
    {
        $rating = Rating::maxRating();

        $results = $this->db::table('flashcards')
            ->latest()
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $search = mb_strtolower($search);

                    return $q->where(DB::raw('LOWER(flashcards.front_word)'), 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('LOWER(flashcards.back_word)'), 'LIKE', '%' . $search . '%');
                });
            })
            ->when($deck_id !== null, fn ($q) => $q->where('flashcards.flashcard_deck_id', $deck_id->getValue()))
            ->when($owner !== null, fn ($q) => $q->where('flashcards.user_id', $owner->getId()))
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->selectRaw("
                flashcards.*,
                (SELECT learning_session_flashcards.rating
                 FROM learning_session_flashcards 
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                 AND rating is not null
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
                    (float) $data->rating_ratio * 100
                );
            })->toArray();

        return $results;
    }
}
