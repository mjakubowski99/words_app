<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Domain\Models\Rating;
use Illuminate\Support\Arr;
use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class FlashcardMapper
{
    use HasOwnerBuilder;

    public function __construct(private readonly DB $db) {}

    public function getByCategory(FlashcardDeckId $id): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcards(UserId $user_id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.user_id', $user_id->getValue())
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->whereNotIn('flashcards.id', $exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcardsByCategory(FlashcardDeckId $id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->whereNotIn('flashcards.id', $exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function createMany(array $flashcards): array
    {
        $insert_data = [];
        $now = now();

        /** @var Flashcard $flashcard */
        foreach ($flashcards as $flashcard) {
            $insert_data[] = [
                'user_id' => $flashcard->getOwner()->isUser() ? $flashcard->getOwner()->getId() : null,
                'admin_id' => $flashcard->getOwner()->isAdmin() ? $flashcard->getOwner()->getId() : null,
                'flashcard_deck_id' => $flashcard->getDeck()->getId(),
                'front_word' => $flashcard->getFrontWord(),
                'front_lang' => $flashcard->getFrontLang()->getValue(),
                'back_word' => $flashcard->getBackWord(),
                'back_lang' => $flashcard->getBackLang()->getValue(),
                'front_context' => $flashcard->getFrontContext(),
                'back_context' => $flashcard->getBackContext(),
                'language_level' => $flashcard->getLanguageLevel()->value,
                'emoji' => $flashcard->getEmoji()?->toUnicode(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $results = $this->db::table('flashcards')
            ->insertReturning($insert_data, ['id', 'front_word', 'back_word']);

        foreach ($flashcards as $flashcard) {
            Arr::first($results, function ($result) use ($flashcard) {
                if ($result->front_word === $flashcard->getFrontWord() && $result->back_word === $flashcard->getBackWord()) {
                    $flashcard->setId(new FlashcardId($result->id));
                    return true;
                }
                return false;
            });
        }
        return $flashcards;
    }

    public function findMany(array $flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->whereIn('flashcards.id', $flashcard_ids)
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->all();
    }

    public function findManyForUser(array $flashcard_ids, UserId $user_id): array
    {
        return $this->db::table('flashcards')
            ->whereIn('flashcards.id', $flashcard_ids)
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->leftJoin('sm_two_flashcards', 'sm_two_flashcards.flashcard_id', '=', 'flashcards.id')
            ->where(fn($q) => $q->where('sm_two_flashcards.user_id', '=', $user_id->getValue())->orWhereNull('sm_two_flashcards.user_id'))
            ->select(
                'flashcards.*',
                'sm_two_flashcards.last_rating',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->all();
    }

    public function delete(FlashcardId $id): void
    {
        $this->db::table('flashcards')
            ->where('id', $id)
            ->delete();
    }

    /** @param FlashcardId[] $flashcard_ids */
    public function bulkDelete(UserId $user_id, array $flashcard_ids): void
    {
        $this->db::table('flashcards')
            ->where('user_id', $user_id)
            ->whereIn('id', $flashcard_ids)
            ->delete();
    }

    public function getStoryIdForFlashcards(array $flashcard_ids): array
    {
        $story_ids = $this->db::table('story_flashcards')
            ->whereIn('flashcard_id', $flashcard_ids)
            ->selectRaw('DISTINCT story_id')
            ->pluck('story_id');

        return $story_ids->map(function ($story_id) {
            return new StoryId($story_id);
        })->toArray();
    }

    public function deleteAllForUser(UserId $user_id): void
    {
        $this->db::table('flashcards')
            ->where('user_id', $user_id)
            ->delete();
    }

    public function update(Flashcard $flashcard): void
    {
        $now = now();

        $this->db::table('flashcards')
            ->where('id', $flashcard->getId())
            ->update([
                'user_id' => $flashcard->getOwner()->isUser() ? $flashcard->getOwner()->getId() : null,
                'admin_id' => $flashcard->getOwner()->isAdmin() ? $flashcard->getOwner()->getId() : null,
                'flashcard_deck_id' => $flashcard->getDeck()->getId(),
                'front_word' => $flashcard->getFrontWord(),
                'front_lang' => $flashcard->getFrontLang()->getValue(),
                'back_word' => $flashcard->getBackWord(),
                'back_lang' => $flashcard->getBackLang()->getValue(),
                'front_context' => $flashcard->getFrontContext(),
                'back_context' => $flashcard->getBackContext(),
                'language_level' => $flashcard->getLanguageLevel()->value,
                'emoji' => $flashcard->getEmoji()?->toUnicode(),
                'updated_at' => $now,
            ]);
    }

    public function replaceDeck(FlashcardDeckId $actual_deck_id, FlashcardDeckId $new_deck_id): bool
    {
        $this->db::table('flashcards')
            ->where('flashcard_deck_id', $actual_deck_id)
            ->update([
                'flashcard_deck_id' => $new_deck_id,
                'updated_at' => now(),
            ]);

        return true;
    }

    public function replaceInSessions(FlashcardDeckId $actual_deck_id, FlashcardDeckId $new_deck_id): bool
    {
        $this->db::table('learning_sessions')
            ->where('flashcard_deck_id', $actual_deck_id)
            ->update([
                'flashcard_deck_id' => $new_deck_id,
                'updated_at' => now(),
            ]);

        return true;
    }

    public function hasAnySessions(FlashcardId $id): bool
    {
        return $this->db::table('learning_session_flashcards')
            ->where('flashcard_id', $id->getValue())
            ->exists();
    }

    public function map(object $data): Flashcard
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
            Language::from($data->front_lang),
            $data->back_word,
            Language::from($data->back_lang),
            $data->front_context,
            $data->back_context,
            $this->buildOwner((string) $data->user_id, (string) $data->admin_id),
            $deck,
            LanguageLevel::from($data->language_level),
            $data->emoji ? Emoji::fromUnicode($data->emoji) : null,
            isset($data->last_rating) ? Rating::from($data->last_rating) : null,
        );
    }
}
