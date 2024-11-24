<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v1;

use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\OwnerCategoryRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\FlashcardCategoriesResource',
    properties: [
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
class FlashcardDecksResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var array $user_categories */
        $user_categories = $this->resource['decks'];

        $page = $this->resource['page'];
        $per_page = $this->resource['per_page'];

        return [
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
