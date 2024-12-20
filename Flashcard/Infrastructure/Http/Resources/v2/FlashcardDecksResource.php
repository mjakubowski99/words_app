<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\OwnerCategoryRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\FlashcardDecksResource',
    properties: [
        new OAT\Property(
            property: 'decks',
            description: 'List of flashcard decks',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'id',
                        description: 'Deck id',
                        type: 'integer',
                        example: 11,
                    ),
                    new OAT\Property(
                        property: 'name',
                        description: 'Deck name',
                        type: 'string',
                        example: 'Two people talk'
                    ),
                    new OAT\Property(
                        property: 'language_level',
                        ref: '#/components/schemas/LanguageLevel'
                    ),
                    new OAT\Property(
                        property: 'flashcards_count',
                        description: 'Count of flashcards in deck',
                        type: 'integer',
                        example: 1,
                    ),
                    new OAT\Property(
                        property: 'last_learnt_at',
                        ref: '#/components/schemas/date_time',
                        description: 'Timestamp of last learning of this deck'
                    ),
                    new OAT\Property(
                        property: 'rating_percentage',
                        description: 'Rating percentage which tells how good user knows this deck',
                        type: 'float',
                        example: 66.66,
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
        /** @var array $user_decks */
        $user_decks = $this->resource['decks'];
        $page = $this->resource['page'];
        $per_page = $this->resource['per_page'];

        return [
            'decks' => array_map(function (OwnerCategoryRead $resource) {
                return [
                    'id' => $resource->getId()->getValue(),
                    'name' => $resource->getName(),
                    'language_level' => $resource->getLanguageLevel()->value,
                    'flashcards_count' => $resource->getFlashcardsCount(),
                    'last_learnt_at' => $resource->getLastLearntAt() ? $resource->getLastLearntAt()->toDateTimeString() : null,
                    'rating_percentage' => $resource->getRatingPercentage(),
                ];
            }, $user_decks),
            'page' => $page,
            'per_page' => $per_page,
        ];
    }
}
