<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class FlashcardDeckMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
    ) {}

    public function create(Deck $deck): FlashcardDeckId
    {
        $now = now();

        $result = $this->db::table('flashcard_decks')
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

        $this->db::table('flashcard_decks')
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

    public function searchByName(UserId $user_id, string $name): ?Deck
    {
        $result = $this->db::table('flashcard_decks')
            ->where('user_id', $user_id->getValue())
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        return $result ? $this->map($result) : null;
    }

    public function getByUser(UserId $user_id, int $page, int $per_page): array
    {
        $results = $this->db::table('flashcard_decks')
            ->where('user_id', $user_id->getValue())
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->get()
            ->toArray();

        return array_map(fn (object $result) => $this->map($result), $results);
    }

    public function remove(FlashcardDeckId $id): void
    {
        $this->db::table('flashcard_decks')
            ->where('id', $id->getValue())
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
