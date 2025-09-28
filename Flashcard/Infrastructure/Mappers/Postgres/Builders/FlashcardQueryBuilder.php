<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres\Builders;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;

class FlashcardQueryBuilder extends CustomQueryBuilder
{
    public static function tableName(): string
    {
        return 'flashcards';
    }

    public function byUser(UserId $user_id): static
    {
        return $this->where('flashcards.user_id', $user_id->getValue())
            ->whereNull('flashcards.admin_id');
    }

    public function byIds(array $flashcard_ids): self
    {
        return $this->whereIn('flashcards.id', $flashcard_ids);
    }

    public function without(array $flashcard_ids): self
    {
        return $this->whereNotIn('flashcards.id', $flashcard_ids);
    }

    public function leftJoinSmTwoFlashcards(UserId $user_id): self
    {
        return $this->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->where(fn ($q) => $q->where('sm_two_flashcards.user_id', '=', $user_id->getValue())
                ->orWhereNull('sm_two_flashcards.user_id'));
    }

    public function leftJoinDeck(): self
    {
        return $this->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id');
    }

    public function byDeckIds(array $deck_ids): self
    {
        return $this->whereIn('flashcards.flashcard_deck_id', $deck_ids);
    }

    public function joinAvgRatings(UserId $user_id, int $ratings_limit, string $alias): self
    {
        $raned_ratings_query = LearningSessionFlashcardQueryBuilder::new()
            ->joinLearningSessions()
            ->byUser($user_id)
            ->rated()
            ->addSelectColumn('flashcard_id')
            ->addSelectColumn('rating')
            ->addSelect(DB::raw('ROW_NUMBER() OVER (
            PARTITION BY learning_session_flashcards.flashcard_id 
            ORDER BY learning_session_flashcards.id DESC
        ) as rn'));

        $avg_ratings_query = DB::table(DB::raw("({$raned_ratings_query->toSql()}) as ranked_ratings"))
            ->mergeBindings($raned_ratings_query)
            ->select('flashcard_id')
            ->selectRaw('AVG(rating)::float as avg_rating')
            ->where('rn', '<=', $ratings_limit)
            ->groupBy('flashcard_id');

        return $this->joinSub($avg_ratings_query, $alias, function ($join) use ($alias) {
            $join->on($alias . '.flashcard_id', '=', 'flashcards.id');
        });
    }

    public function addSelectAvgRatings(string $alias): self
    {
        return $this->addSelect(DB::raw("SUM(avg_ratings.avg_rating) as {$alias}"));
    }

    public function addSelectDeckColumns(array $columns): self
    {
        foreach ($columns as $column => $alias) {
            $this->addSelect("flashcard_decks.{$column} as {$alias}");
        }

        return $this;
    }
}
