<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;

class FlashcardDeckMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function create(Deck $deck): FlashcardDeckId
    {
        $now = now();

        $result = $this->db::table('flashcard_decks')
            ->insertGetId([
                'user_id' => $deck->getOwner()->getId(),
                'tag' => $deck->getName(),
                'name' => $deck->getName(),
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
                'user_id' => $deck->getOwner()->getId(),
                'tag' => $deck->getName(),
                'name' => $deck->getName(),
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

    public function searchByName(Owner $owner, string $name): ?Deck
    {
        /* @phpstan-ignore-next-line */
        if ($owner->getOwnerType() !== FlashcardOwnerType::USER) {
            throw new ModelNotFoundException('This owner does not have decks');
        }

        $result = $this->db::table('flashcard_decks')
            ->where('user_id', $owner->getId())
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        return $result ? $this->map($result) : null;
    }

    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        $results = $this->db::table('flashcard_decks')
            ->where('user_id', $owner->getId()->getValue())
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
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $data->tag,
            $data->name,
        ))->init(new FlashcardDeckId($data->id));
    }
}
