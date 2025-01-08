<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
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

    public function createMany(array $flashcards): void
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
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db::table('flashcards')->insert($insert_data);
    }

    public function find(FlashcardId $id): Flashcard
    {
        $result = $this->db::table('flashcards')
            ->where('flashcards.id', $id)
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'flashcards.flashcard_deck_id')
            ->select(
                'flashcards.*',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->first();

        if (!$result) {
            throw new ModelNotFoundException();
        }

        return $this->map($result);
    }

    public function delete(FlashcardId $id): void
    {
        $this->db::table('flashcards')
            ->where('id', $id)
            ->delete();
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
        );
    }
}
