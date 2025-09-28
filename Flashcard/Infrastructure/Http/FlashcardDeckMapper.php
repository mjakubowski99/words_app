<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http;

use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;
use Flashcard\Infrastructure\Mappers\Postgres\Builders\FlashcardDeckQueryBuilder;

class FlashcardDeckMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
    ) {}

    public function create(Deck $deck): FlashcardDeckId
    {
        $now = now();

        $result = FlashcardDeckQueryBuilder::new()
            ->insertGetId([
                'user_id' => $deck->getOwner()->isUser() ? $deck->getOwner()->getId() : null,
                'admin_id' => $deck->getOwner()->isAdmin() ? $deck->getOwner()->getId() : null,
                'tag' => $deck->getName(),
                'name' => $deck->getName(),
                'default_language_level' => $deck->getDefaultLanguageLevel(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        return new FlashcardDeckId($result);
    }

    public function update(Deck $deck): void
    {
        $now = now();

        FlashcardDeckQueryBuilder::new()
            ->where('id', $deck->getId()->getValue())
            ->update([
                'user_id' => $deck->getOwner()->isUser() ? $deck->getOwner()->getId() : null,
                'admin_id' => $deck->getOwner()->isAdmin() ? $deck->getOwner()->getId() : null,
                'tag' => $deck->getName(),
                'name' => $deck->getName(),
                'default_language_level' => $deck->getDefaultLanguageLevel(),
                'updated_at' => $now,
            ]);
    }

    public function updateLastViewedAt(FlashcardDeckId $id, UserId $user_id): void
    {
        $this->db::table('flashcard_deck_activities')
            ->where('flashcard_deck_id', $id->getValue())
            ->where('user_id', $user_id->getValue())
            ->upsert([
                'user_id' => $user_id->getValue(),
                'flashcard_deck_id' => $id->getValue(),
                'last_viewed_at' => now(),
            ], [
                'user_id',
                'flashcard_deck_id',
            ], ['last_viewed_at']);
    }

    public function findById(FlashcardDeckId $id): Deck
    {
        $result = $this->db::table('flashcard_decks')
            ->where('id', $id->getValue())
            ->first();

        if (!$result) {
            throw new ModelNotFoundException('Deck not found');
        }

        return $this->map($result);
    }

    public function searchByName(UserId $user_id, string $name, Language $front_lang, Language $back_lang): ?Deck
    {
        $result = FlashcardDeckQueryBuilder::new()
            ->byUser($user_id)
            ->byLanguage($front_lang, $back_lang)
            ->byName($name)
            ->first();

        return $result ? $this->map($result) : null;
    }

    public function searchByNameAdmin(string $name, Language $front_lang, Language $back_lang): ?Deck
    {
        $result = FlashcardDeckQueryBuilder::new()
            ->byLanguage($front_lang, $back_lang)
            ->byAdmin()
            ->byName($name)
            ->first();

        return $result ? $this->map($result) : null;
    }

    public function getByUser(UserId $user_id, Language $front_lang, Language $back_lang, int $page, int $per_page): array
    {
        $results = FlashcardDeckQueryBuilder::new()
            ->byLanguage($front_lang, $back_lang)
            ->byUser($user_id)
            ->setPage($page, $per_page)
            ->get()
            ->toArray();

        return array_map(fn (object $result) => $this->map($result), $results);
    }

    public function remove(FlashcardDeckId $id): void
    {
        FlashcardDeckQueryBuilder::new()
            ->where('id', $id->getValue())
            ->delete();
    }

    public function deleteAllForUser(UserId $user_id): void
    {
        FlashcardDeckQueryBuilder::new()
            ->byUser($user_id)
            ->delete();
    }

    public function bulkDelete(UserId $user_id, array $deck_ids): void
    {
        FlashcardDeckQueryBuilder::new()
            ->byUser($user_id)
            ->whereIn('id', $deck_ids)
            ->delete();
    }

    private function map(object $data): Deck
    {
        return (new Deck(
            $this->buildOwner((string) $data->user_id, (string) $data->admin_id),
            $data->tag,
            $data->name,
            LanguageLevel::from($data->default_language_level),
        ))->init(new FlashcardDeckId($data->id));
    }
}
