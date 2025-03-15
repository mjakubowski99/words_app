<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\GeneralRating;
use Flashcard\Application\ReadModels\RatingStatsRead;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

class FlashcardReadMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
    ) {}

    public function findFlashcardStats(?FlashcardDeckId $deck_id, ?UserId $user_id): RatingStatsReadCollection
    {
        $results = $this->db::table('flashcards')
            ->when($deck_id !== null, fn ($q) => $q->where('flashcards.flashcard_deck_id', '=', $deck_id->getValue()))
            ->when($user_id !== null, fn ($q) => $q->where('learning_sessions.user_id', '=', $user_id->getValue()))
            ->leftJoin(
                'learning_session_flashcards',
                'learning_session_flashcards.flashcard_id',
                '=',
                'flashcards.id'
            )
            ->leftJoin('learning_sessions', 'learning_sessions.id', '=', 'learning_session_flashcards.learning_session_id')
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

    public function getByUser(UserId $user_id, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        $user_flashcards = $this->search($user_id, null, $user_id, $search, $page, $per_page);

        $count = $this->db::table('flashcards')
            ->where('user_id', $user_id->getValue())
            ->count();

        return new UserFlashcardsRead(
            $user_id,
            $user_flashcards,
            $page,
            $per_page,
            $count
        );
    }

    public function search(UserId $user_id, ?FlashcardDeckId $deck_id, ?UserId $user_filter, ?string $search, int $page, int $per_page): array
    {
        $rating = Rating::maxRating();

        $results = $this->db::table('flashcards')
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $search = mb_strtolower($search);

                    return $q->where(DB::raw('LOWER(flashcards.front_word)'), 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('LOWER(flashcards.back_word)'), 'LIKE', '%' . $search . '%');
                });
            })
            ->when($deck_id !== null, fn ($q) => $q->where('flashcards.flashcard_deck_id', $deck_id->getValue()))
            ->when($user_filter !== null, fn ($q) => $q->where('flashcards.user_id', $user_filter->getValue()))
            ->orderBy('flashcards.front_word')
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->selectRaw("
                flashcards.*,
                (SELECT learning_session_flashcards.rating
                 FROM learning_session_flashcards
                 INNER JOIN learning_sessions ON learning_sessions.id = learning_session_flashcards.learning_session_id
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                 AND rating is not null
                 AND learning_sessions.user_id = '{$user_id->getValue()}'
                 ORDER BY learning_session_flashcards.updated_at DESC
                 LIMIT 1
                ) as last_rating,
                (SELECT AVG(COALESCE(rating,0)/{$rating}::float)
                 FROM learning_session_flashcards 
                 INNER JOIN learning_sessions ON learning_sessions.id = learning_session_flashcards.learning_session_id 
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                   AND learning_sessions.user_id = '{$user_id->getValue()}'
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
                    (float) $data->rating_ratio * 100,
                    $data->emoji ? Emoji::fromUnicode($data->emoji) : null,
                    $this->buildOwner($data->user_id, $data->admin_id)->getOwnerType(),
                );
            })->toArray();

        return $results;
    }
}
