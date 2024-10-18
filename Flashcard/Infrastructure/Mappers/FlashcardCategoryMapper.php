<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\Exceptions\ModelNotFoundException;

class FlashcardCategoryMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function create(ICategory $category): CategoryId
    {
        $now = now();

        $result = $this->db::table('flashcard_categories')
            ->insertGetId([
                'user_id' => $category->getOwner()->getId(),
                'tag' => $category->getName(),
                'name' => $category->getName(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        return new CategoryId($result);
    }

    public function findById(CategoryId $id): Category
    {
        $result = $this->db::table('flashcard_categories')
            ->where('id', $id->getValue())
            ->first();

        if (!$result) {
            throw new ModelNotFoundException('Category not found');
        }

        return $this->map($result);
    }

    public function searchByName(Owner $owner, string $name): ?Category
    {
        /* @phpstan-ignore-next-line */
        if ($owner->getOwnerType() !== FlashcardOwnerType::USER) {
            throw new ModelNotFoundException('This owner does not have categories');
        }

        $result = $this->db::table('flashcard_categories')
            ->where('user_id', $owner->getId())
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        return $result ? $this->map($result) : null;
    }

    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        $results = $this->db::table('flashcard_categories')
            ->where('user_id', $owner->getId()->getValue())
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->get()
            ->toArray();

        return array_map(fn (object $result) => $this->map($result), $results);
    }

    public function remove(CategoryId $id): void
    {
        $this->db::table('flashcard_categories')
            ->where('id', $id->getValue())
            ->delete();
    }

    private function map(object $data): Category
    {
        return (new Category(
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $data->tag,
            $data->name,
        ))->init(new CategoryId($data->id));
    }
}
