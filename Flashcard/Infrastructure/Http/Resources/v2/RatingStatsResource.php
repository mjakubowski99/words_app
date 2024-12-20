<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use OpenApi\Attributes as OAT;
use Shared\Enum\GeneralRatingType;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\RatingStatsRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\DeckRatingStatsResource',
    properties: [
        new OAT\Property(
            property: 'rating',
            description: 'Get general rating of flashcard. (NEW means that flashcard is not rated)',
            type: 'string',
            enum: [
                GeneralRatingType::VERY_GOOD,
                GeneralRatingType::GOOD,
                GeneralRatingType::WEAK,
                GeneralRatingType::UNKNOWN,
            ],
            example: 'VERY_GOOD',
        ),
        new OAT\Property(
            property: 'rating_percentage',
            description: 'Percentage of flashcards with rating given in rating property',
            type: 'float',
            example: 10.0,
        ),
    ]
)]
/**
 * @property RatingStatsReadCollection $resource
 */
class RatingStatsResource extends JsonResource
{
    public function toArray($request): array
    {
        return array_map(function (RatingStatsRead $stats) {
            return [
                'rating' => $stats->getRating()->getValue(),
                'rating_percentage' => $stats->getRatingPercentage(),
            ];
        }, $this->resource->getRatingStats());
    }
}
