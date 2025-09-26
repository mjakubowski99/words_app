<?php

namespace Flashcard\Infrastructure\Mappers\Postgres\Builders;

use Illuminate\Support\Facades\DB;
use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\UserId;

class FlashcardDeckQueryBuilder extends CustomQueryBuilder
{
    public static function tableName(): string
    {
        return 'flashcard_decks';
    }

    public function searchByName(?string $search): static
    {
        return $this->when(!is_null($search), function ($query) use ($search) {
            return $query->where(DB::raw('LOWER(flashcard_decks.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
        });
    }

    public function byUser(UserId $user_id): static
    {
        return $this->where("flashcard_decks.user_id", $user_id->getValue())
            ->whereNull("flashcard_decks.admin_id");
    }

    public function byAdmin(): static
    {
        return $this->whereNotNull('flashcard_decks.admin_id')
            ->whereNull('flashcard_decks.user_id');
    }

    public function byLanguage(Language $front_lang, Language $back_lang): static
    {
        return $this->whereExists(function ($query) use ($front_lang, $back_lang) {
            $query->select('flashcards.id')
                ->from('flashcards')
                ->whereColumn('flashcards.flashcard_deck_id', 'flashcard_decks.id')
                ->where('flashcards.front_lang', $front_lang->value)
                ->where('flashcards.back_lang', $back_lang->value);
            }
        );
    }

    public function byLanguageLevel(?LanguageLevel $level)
    {
        return $this->when($level !== null, fn ($q) => $q->where('flashcard_decks.default_language_level', '=', $level->value));
    }

    public function joinActivities(UserId $user_id): static
    {
        $activities = DB::table('flashcard_deck_activities')->where('user_id', $user_id);

        return $this->leftJoinSub($activities, 'flashcard_deck_activities', function ($join) {
            $join->on('flashcard_deck_activities.flashcard_deck_id', '=', 'flashcard_decks.id');
        });
    }

    public function addSelectMostFrequentLanguageLevel(string $alias): static
    {
        return $this->addSelect(DB::raw("(SELECT language_level
                    FROM flashcards
                    WHERE flashcards.flashcard_deck_id = flashcard_decks.id
                    GROUP BY language_level
                    ORDER BY COUNT(*) DESC
                LIMIT 1) as {$alias}")
        );
    }

    public function addSelectFlashcardsCount(string $alias): static
    {
        return $this->addSelect(DB::raw("(
            SELECT COUNT(flashcards.id)
            FROM flashcards
            WHERE flashcards.flashcard_deck_id = flashcard_decks.id
        ) as {$alias}"));
    }

    public function addSelectLastLearntAt(UserId $user_id, string $alias): static
    {
        return $this->addSelect(DB::raw("(
            SELECT MAX(lsf.updated_at)
            FROM learning_session_flashcards as lsf
            LEFT JOIN flashcards ON lsf.flashcard_id = flashcards.id
            LEFT JOIN learning_sessions as ls on ls.id = lsf.learning_session_id
            WHERE flashcards.flashcard_deck_id = flashcard_decks.id
            AND ls.user_id = '{$user_id->getValue()}'
            ) as last_learnt_at"
        ));
    }

    public function orderByActivitiesAndName(): static
    {
        return $this->orderByRaw('
            flashcard_deck_activities.last_viewed_at DESC NULLS LAST,
            flashcard_decks.name ASC
        ');
    }

    public function orderByLastActivity(): static
    {
        return $this->orderByRaw('
            CASE
                WHEN flashcard_deck_activities.last_viewed_at IS NOT NULL THEN flashcard_deck_activities.last_viewed_at
                ELSE flashcard_decks.created_at
            END DESC NULLS LAST
        ');
    }
}