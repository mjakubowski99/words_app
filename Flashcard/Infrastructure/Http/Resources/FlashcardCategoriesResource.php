<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\DTO\MainFlashcardCategoryDTO;
use Flashcard\Application\DTO\UserFlashcardCategoryDTO;

class FlashcardCategoriesResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var MainFlashcardCategoryDTO $main_category */
        $main_category = $this->resource['main'];

        /** @var UserFlashcardCategoryDTO[] $user_categories */
        $user_categories = $this->resource['categories'];
        $page = $this->resource['page'];
        $per_page = $this->resource['per_page'];

        return [
            'main' => [
                'id' => $main_category->getId()->getValue(),
                'name' => $main_category->getName(),
            ],
            'categories' => array_map(function (UserFlashcardCategoryDTO $resource) {
                return [
                    'id' => $resource->getId(),
                    'name' => $resource->getName(),
                ];
            }, $user_categories),
            'page' => $page,
            'per_page' => $per_page,
        ];
    }
}
