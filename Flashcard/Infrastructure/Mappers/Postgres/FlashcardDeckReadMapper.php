<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Carbon\Carbon;
use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Illuminate\Support\Collection;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;
use Flashcard\Infrastructure\Mappers\Postgres\Builders\FlashcardQueryBuilder;
use Flashcard\Infrastructure\Mappers\Postgres\Builders\FlashcardDeckQueryBuilder;

class FlashcardDeckReadMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly FlashcardReadMapper $flashcard_mapper,
    ) {}

    public function findDetails(UserId $user_id, FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        $deck = $this->findDeck($id, $user_id);
        if (!$deck) {
            throw new ModelNotFoundException('Category not found');
        }

        $flashcards = $this->flashcard_mapper->search($user_id, null, null, $id, null, $search, $page, $per_page);

        $flashcards_count = FlashcardQueryBuilder::new()->byDeckIds([$id->getValue()])->count();

        return $this->mapToDetails($deck, $flashcards, $page, $per_page, $flashcards_count, $user_id);
    }

    /** @return OwnerCategoryRead[] */
    public function getAdminDecks(UserId $user_id, Language $front_lang, Language $back_lang, ?LanguageLevel $level, ?string $search, int $page, int $per_page): array
    {
        $results = FlashcardDeckQueryBuilder::new()
            ->byLanguage($front_lang, $back_lang)
            ->byLanguageLevel($level)
            ->byAdmin()
            ->searchByName($search)
            ->setPage($page, $per_page)
            ->joinActivities($user_id)
            ->addSelectAll(['*'])
            ->addSelectFlashcardsCount('flashcards_count')
            ->addSelectLastLearntAt($user_id, 'last_learnt_at')
            ->addSelectMostFrequentLanguageLevel('most_frequent_language_level')
            ->orderByActivitiesAndName()
            ->get();

        return $this->mapResults($results, $user_id, FlashcardOwnerType::ADMIN);
    }

    /** @return OwnerCategoryRead[] */
    public function getByUser(UserId $user_id, Language $front_lang, Language $back_lang, ?string $search, int $page, int $per_page): array
    {
        $results = FlashcardDeckQueryBuilder::new()
            ->byLanguage($front_lang, $back_lang)
            ->byUser($user_id)
            ->searchByName($search)
            ->setPage($page, $per_page)
            ->joinActivities($user_id)
            ->addSelectAll(['*'])
            ->addSelectFlashcardsCount('flashcards_count')
            ->addSelectMostFrequentLanguageLevel('most_frequent_language_level')
            ->addSelectLastLearntAt($user_id, 'last_learnt_at')
            ->orderByLastActivity()
            ->get();

        return $this->mapResults($results, $user_id, FlashcardOwnerType::USER);
    }

    private function findDeck(FlashcardDeckId $id, UserId $user_id): ?object
    {
        return FlashcardDeckQueryBuilder::new()
            ->addSelectAll(['*'])
            ->addSelectMostFrequentLanguageLevel('most_frequent_language_level')
            ->addSelectLastLearntAt($user_id, 'last_learnt_at')
            ->find($id->getValue());
    }

    /** @return OwnerCategoryRead[] */
    private function mapResults(Collection $results, UserId $user_id, FlashcardOwnerType $owner_type): array
    {
        $rating_stats = $this->getRatingStats($results->pluck('id'), $user_id);

        return $results->map(function (object $data) use ($rating_stats, $owner_type) {
            $rating_sum = $rating_stats->where('flashcard_deck_id', $data->id)->first()->total_avg_rating ?? 0.0;

            $avg_rating = $this->calculateAvgRating((float) $rating_sum, $data->flashcards_count);

            return new OwnerCategoryRead(
                new FlashcardDeckId($data->id),
                $data->name,
                LanguageLevel::from($data->most_frequent_language_level ?? $data->default_language_level),
                $data->flashcards_count,
                $avg_rating,
                $data->last_learnt_at ? Carbon::parse($data->last_learnt_at) : null,
                $owner_type
            );
        })->toArray();
    }

    private function mapToDetails(
        object $deck,
        mixed $flashcards,
        int $page,
        int $per_page,
        int $flashcards_count,
        UserId $user_id
    ): DeckDetailsRead {
        $total_avg_rating = $this->getRatingStats(
            collect([$deck->id]),
            $user_id
        )->first()->toal_avg_rating ?? 0.0;

        $avg_rating = $this->calculateAvgRating($total_avg_rating, $flashcards_count);

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

    private function getRatingStats(Collection $ids, UserId $user_id): Collection
    {
        return FlashcardQueryBuilder::new()
            ->joinAvgRatings($user_id, 2, 'avg_ratings')
            ->byDeckIds($ids->toArray())
            ->addSelectAll(['flashcard_deck_id'])
            ->addSelectAvgRatings('total_avg_rating')
            ->groupBy('flashcards.flashcard_deck_id')
            ->get();
    }

    private function calculateAvgRating(float $total_avg_rating, int $flashcards_count): float
    {
        return $flashcards_count ? $total_avg_rating / ($flashcards_count * Rating::maxRating()) * 100.0 : 0;
    }
}
