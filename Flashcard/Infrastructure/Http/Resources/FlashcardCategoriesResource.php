<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\DTO\MainFlashcardCategoryDTO;
use Flashcard\Application\ReadModels\OwnerCategoryRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\FlashcardCategoriesResource',
    properties: [
        new OAT\Property(
            property: 'main',
            properties: [
                new OAT\Property(
                    property: 'id',
                    description: 'ID of the main category',
                    type: 'integer',
                    example: 10,
                ),
                new OAT\Property(
                    property: 'name',
                    description: 'Name of the main category',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
        new OAT\Property(
            property: 'categories',
            description: 'List of flashcard categories',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'id',
                        description: 'Category id',
                        type: 'integer',
                        example: 11,
                    ),
                    new OAT\Property(
                        property: 'name',
                        description: 'Category name',
                        type: 'string',
                        example: 'Two people talk'
                    ),
                ],
                type: 'object'
            )
        ),
        new OAT\Property(
            property: 'page',
            type: 'integer',
            example: 1,
        ),
        new OAT\Property(
            property: 'per_page',
            type: 'integer',
            example: 15,
        ),
    ]
)]
class FlashcardCategoriesResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var MainFlashcardCategoryDTO $main_category */
        $main_category = $this->resource['main'];

        /** @var array $user_categories */
        $user_categories = $this->resource['categories'];

        $page = $this->resource['page'];
        $per_page = $this->resource['per_page'];

        return [
            'main' => [
                'id' => $main_category->getId()->getValue(),
                'name' => $main_category->getName(),
            ],
            'categories' => array_map(function (OwnerCategoryRead $resource) {
                return [
                    'id' => $resource->getId()->getValue(),
                    'name' => $resource->getName(),
                ];
            }, $user_categories),
            'page' => $page,
            'per_page' => $per_page,
        ];
    }
}
