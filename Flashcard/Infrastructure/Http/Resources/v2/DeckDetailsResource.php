<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use Shared\Enum\Language;
use OpenApi\Attributes as OAT;
use Shared\Enum\GeneralRatingType;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Shared\Enum\LanguageLevel;

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
    ]
)]
/**
 * @property DeckDetailsRead $resource
 */
class DeckDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var DeckDetailsRead $category */
        $category = $this->resource['details'];

        /** @var int $page */
        $page = $this->resource['page'];

        /** @var int $page */
        $per_page = $this->resource['per_page'];

        return [
            'id' => $category->getId()->getValue(),
            'name' => $category->getName(),
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
                ];
            }, $category->getFlashcards()),
            'page' => $page,
            'per_page' => $per_page,
        ];
    }
}
