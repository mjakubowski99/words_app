<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\UserId;

class FlashcardCategoryMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function create(ICategory $category): CategoryId
    {
        $result = $this->db::table('flashcard_categories')
            ->insertGetId([
                'user_id' => $category->getOwner()->getId(),
                'tag' => $category->getTag(),
                'name' => $category->getName(),
            ]);

        return new CategoryId($result);
    }

    public function findById(CategoryId $id): Category
    {
        $result = $this->db::table('flashcard_categories')
            ->where('id', $id->getValue())
            ->first();

        return $this->map($result);
    }

    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        $results = $this->db::table('flashcard_categories')
            ->where('user_id', $owner->getId()->getValue())
            ->take($per_page)
            ->skip(($page-1) * $per_page)
            ->get()
            ->toArray();

        return array_map(fn (object $result) => $this->map($result), $results);
    }

    public function findByTag(string $tag): Category
    {
        $result = $this->db::table('flashcard_categories')
            ->where('tag', $tag)
            ->first();

        return $this->map($result);
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
