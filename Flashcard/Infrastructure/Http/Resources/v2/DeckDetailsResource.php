<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use Shared\Enum\Language;
use OpenApi\Attributes as OAT;
use Shared\Enum\LanguageLevel;
use Shared\Enum\GeneralRatingType;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\DeckDetailsRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\DeckDetailsResource',
    properties: [
        new OAT\Property(
            property: 'id',
            description: 'ID of the session',
            type: 'integer',
            example: 10,
        ),
        new OAT\Property(
            property: 'name',
            description: 'Category name',
            type: 'string',
            example: 'Two people talk',
        ),
        new OAT\Property(
            property: 'language_level',
            ref: '#/components/schemas/LanguageLevel'
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
        new OAT\Property(
            property: 'owner_type',
            ref: '#/components/schemas/FlashcardOwnerType'
        ),
        new OAT\Property(
            property: 'flashcards',
            description: 'List of flashcards in the session',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'id',
                        description: 'Flashcard ID',
                        type: 'integer',
                        example: 11,
                    ),
                    new OAT\Property(
                        property: 'front_word',
                        description: 'Word on the front of flashcard',
                        type: 'string',
                        example: 'Polish'
                    ),
                    new OAT\Property(
                        property: 'front_lang',
                        description: 'Language of the front word',
                        enum: [Language::EN, Language::PL],
                        example: Language::EN,
                    ),
                    new OAT\Property(
                        property: 'back_word',
                        description: 'Word on back of flashcard',
                        type: 'string',
                        example: 'Polska',
                    ),
                    new OAT\Property(
                        property: 'back_lang',
                        description: 'Language of the back word',
                        type: 'string',
                        enum: [Language::EN, Language::PL],
                        example: Language::PL,
                    ),
                    new OAT\Property(
                        property: 'front_context',
                        description: 'Context sentence for the word on front of flashcard',
                        type: 'string',
                        example: 'Country in the center of Europe'
                    ),
                    new OAT\Property(
                        property: 'back_context',
                        description: 'Context sentence for the word on back of flashcard',
                        type: 'string',
                        example: 'Kraj w centrum Europy'
                    ),
                    new OAT\Property(
                        property: 'rating',
                        description: 'Get general rating of flashcard. (NEW means that flashcard is not rated)',
                        type: 'string',
                        enum: [
                            GeneralRatingType::NEW,
                            GeneralRatingType::VERY_GOOD,
                            GeneralRatingType::GOOD,
                            GeneralRatingType::WEAK,
                            GeneralRatingType::UNKNOWN,
                        ],
                        example: 'NEW',
                    ),
                    new OAT\Property(
                        property: 'language_level',
                        description: 'Language level. Default value is: ' . LanguageLevel::DEFAULT,
                        type: 'string',
                        enum: [
                            LanguageLevel::A1,
                            LanguageLevel::A2,
                            LanguageLevel::B1,
                            LanguageLevel::B1,
                            LanguageLevel::C1,
                            LanguageLevel::C2,
                        ],
                        example: LanguageLevel::C1,
                        nullable: true
                    ),
                    new OAT\Property(
                        property: 'rating_percentage',
                        description: 'Rating percentage which tells how good user knows this flashcard',
                        type: 'float',
                        example: 66.66,
                    ),
                    new OAT\Property(
                        property: 'emoji',
                        description: 'Flashcard context emoji',
                        type: 'string',
                        example: '❤️',
                        nullable: true
                    ),
                    new OAT\Property(
                        property: 'owner_type',
                        ref: '#/components/schemas/FlashcardOwnerType'
                    ),
                ],
                type: 'object'
            ),
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
        new OAT\Property(
            property: 'flashcards_count',
            description: 'Count of flashcards in deck',
            type: 'integer',
            example: 15,
        ),
    ]
)]
/**
 * @property DeckDetailsRead $resource
 */
class DeckDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId()->getValue(),
            'name' => $this->resource->getName(),
            'owner_type' => $this->resource->getOwnerType()->value,
            'language_level' => $this->resource->getLanguageLevel()->value,
            'last_learnt_at' => $this->resource->getLastLearntAt(),
            'rating_percentage' => $this->resource->getRatingPercentage(),
            'flashcards' => array_map(function (FlashcardRead $flashcard) {
                return [
                    'id' => $flashcard->getId()->getValue(),
                    'front_word' => $flashcard->getFrontWord(),
                    'front_lang' => $flashcard->getFrontLang()->getValue(),
                    'back_word' => $flashcard->getBackWord(),
                    'back_lang' => $flashcard->getBackLang()->getValue(),
                    'front_context' => $flashcard->getFrontContext(),
                    'back_context' => $flashcard->getBackContext(),
                    'rating' => $flashcard->getGeneralRating()->getValue()->value,
                    'language_level' => $flashcard->getLanguageLevel()->value,
                    'rating_percentage' => $flashcard->getRatingPercentage(),
                    'emoji' => $flashcard->getEmoji(),
                    'owner_type' => $flashcard->getOwnerType()->value,
                ];
            }, $this->resource->getFlashcards()),
            'page' => $this->resource->getPage(),
            'per_page' => $this->resource->getPerPage(),
            'flashcards_count' => $this->resource->getFlashcardsCount(),
        ];
    }
}
