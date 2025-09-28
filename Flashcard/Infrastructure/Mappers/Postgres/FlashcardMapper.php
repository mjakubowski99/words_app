<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;
use Flashcard\Infrastructure\Mappers\Postgres\Builders\FlashcardQueryBuilder;
use Flashcard\Infrastructure\Mappers\Postgres\Builders\LearningSessionQueryBuilder;
use Flashcard\Infrastructure\Mappers\Postgres\Builders\LearningSessionFlashcardQueryBuilder;

class FlashcardMapper
{
    use HasOwnerBuilder;

    public function __construct(private readonly DB $db) {}

    public function getByCategory(FlashcardDeckId $id): array
    {
        return FlashcardQueryBuilder::new()
            ->byDeckIds([$id->getValue()])
            ->leftJoinDeck()
            ->addSelectAll(['*'])
            ->addSelectDeckColumns([
                'user_id' => 'deck_user_id',
                'admin_id' => 'deck_admin_id',
                'tag' => 'deck_tag',
                'name' => 'deck_name',
                'default_language_level' => 'deck_default_language_level',
            ])
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcards(UserId $user_id, int $limit, array $exclude_flashcard_ids): array
    {
        return FlashcardQueryBuilder::new()
            ->leftJoinDeck()
            ->byUser($user_id)
            ->without($exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->addSelectAll(['*'])
            ->addSelectDeckColumns([
                'user_id' => 'deck_user_id',
                'admin_id' => 'deck_admin_id',
                'tag' => 'deck_tag',
                'name' => 'deck_name',
                'default_language_level' => 'deck_default_language_level',
            ])
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcardsByCategory(FlashcardDeckId $id, int $limit, array $exclude_flashcard_ids): array
    {
        return FlashcardQueryBuilder::new()
            ->byDeckIds([$id->getValue()])
            ->leftJoinDeck()
            ->without($exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->addSelectAll(['*'])
            ->addSelectDeckColumns([
                'user_id' => 'deck_user_id',
                'admin_id' => 'deck_admin_id',
                'tag' => 'deck_tag',
                'name' => 'deck_name',
                'default_language_level' => 'deck_default_language_level',
            ])
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
                'emoji' => $flashcard->getEmoji()?->toUnicode(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        FlashcardQueryBuilder::new()->insert($insert_data);
    }

    public function createManyFromStoryFlashcards(StoryCollection $stories): StoryCollection
    {
        [$insert_data, $created_at, $updated_at] = [[], now(), now()];

        /** @var StoryFlashcard $story_flashcard */
        foreach ($stories->getAllStoryFlashcards() as $story_flashcard) {
            $flashcard = $story_flashcard->getFlashcard();

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
                'created_at' => $created_at,
                'updated_at' => $updated_at->addSecond(),
            ];
        }

        /* @phpstan-ignore-next-line */
        $results = $this->db::table('flashcards')
            ->insertReturning($insert_data, ['id', 'front_word', 'back_word', 'updated_at'])
            ->sortBy('updated_at') // ensures correct insert order
            ->values();

        $i = 0;
        foreach ($stories->getAllStoryFlashcards() as $flashcard) {
            $flashcard->getFlashcard()->setId(new FlashcardId($results[$i]->id));
            ++$i;
        }

        return $stories;
    }

    public function findMany(array $flashcard_ids): array
    {
        return FlashcardQueryBuilder::new()
            ->leftJoinDeck()
            ->byIds($flashcard_ids)
            ->addSelectAll(['*'])
            ->addSelectDeckColumns([
                'user_id' => 'deck_user_id',
                'admin_id' => 'deck_admin_id',
                'tag' => 'deck_tag',
                'name' => 'deck_name',
                'default_language_level' => 'deck_default_language_level',
            ])
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->all();
    }

    public function findManyForUser(array $flashcard_ids, UserId $user_id): array
    {
        return FlashcardQueryBuilder::new()
            ->leftJoinDeck()
            ->leftJoinSmTwoFlashcards($user_id)
            ->byIds($flashcard_ids)
            ->addSelectAll(['*'])
            ->addSelect('sm_two_flashcards.last_rating')
            ->addSelectDeckColumns([
                'user_id' => 'deck_user_id',
                'admin_id' => 'deck_admin_id',
                'tag' => 'deck_tag',
                'name' => 'deck_name',
                'default_language_level' => 'deck_default_language_level',
            ])->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->all();
    }

    public function delete(FlashcardId $id): void
    {
        FlashcardQueryBuilder::new()
            ->byIds([$id->getValue()])
            ->delete();
    }

    /** @param FlashcardId[] $flashcard_ids */
    public function bulkDelete(UserId $user_id, array $flashcard_ids): void
    {
        FlashcardQueryBuilder::new()
            ->byUser($user_id)
            ->byIds(array_map(fn (FlashcardId $id) => $id->getValue(), $flashcard_ids))
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
        FlashcardQueryBuilder::new()
            ->byUser($user_id)
            ->delete();
    }

    public function update(Flashcard $flashcard): void
    {
        $now = now();

        FlashcardQueryBuilder::new()
            ->byIds([$flashcard->getId()->getValue()])
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
        FlashcardQueryBuilder::new()
            ->byDeckIds([$actual_deck_id->getValue()])
            ->update([
                'flashcard_deck_id' => $new_deck_id,
                'updated_at' => now(),
            ]);

        return true;
    }

    public function replaceInSessions(FlashcardDeckId $actual_deck_id, FlashcardDeckId $new_deck_id): bool
    {
        LearningSessionQueryBuilder::new()
            ->byDeckId($actual_deck_id)
            ->update([
                'flashcard_deck_id' => $new_deck_id,
                'updated_at' => now(),
            ]);

        return true;
    }

    public function hasAnySessions(FlashcardId $id): bool
    {
        return LearningSessionFlashcardQueryBuilder::new()
            ->byFlashcardId($id)
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
