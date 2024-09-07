<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;

class FlashcardCategoryMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function create(FlashcardCategory $category): CategoryId
    {
        $result = $this->db::table('flashcard_categories')
            ->insertGetId([
                'user_id' => $category->getUserId(),
                'tag' => $category->getTag(),
                'name' => $category->getName(),
            ]);

        return new CategoryId($result);
    }

    public function findById(CategoryId $id): FlashcardCategory
    {
        $result = $this->db::table('flashcard_categories')
            ->where('id', $id->getValue())
            ->first();

        return $this->map($result);
    }

    public function getByUser(UserId $id, int $page, int $per_page): array
    {
        $results = $this->db::table('flashcard_categories')
            ->where('user_id', $id->getValue())
            ->paginate($per_page, ['*'], 'page', $page)
            ->items();

        return array_map(fn (object $result) => $this->map($result), $results);
    }

    public function findByTag(string $tag): FlashcardCategory
    {
        $result = $this->db::table('flashcard_categories')
            ->where('tag', $tag)
            ->first();

        return $this->map($result);
    }

    private function map(object $data): FlashcardCategory
    {
        return (new FlashcardCategory(
            $data->user_id ? UserId::fromString($data->user_id) : null,
            $data->tag,
            $data->name,
        ))->init(new CategoryId($data->id));
    }
}
