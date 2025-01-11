<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use Shared\Enum\Language;
use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\SessionFlashcardRead;
use Flashcard\Application\ReadModels\SessionFlashcardsRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\NextSessionFlashcardsResource',
    properties: [
        new OAT\Property(
            property: 'session',
            properties: [
                new OAT\Property(
                    property: 'id',
                    description: 'ID of the session',
                    type: 'integer',
                    example: 10,
                ),
                new OAT\Property(
                    property: 'cards_per_session',
                    description: 'Number of cards per session',
                    type: 'integer',
                    example: 25,
                ),
                new OAT\Property(
                    property: 'is_finished',
                    description: 'Indicates if the session is finished',
                    type: 'boolean'
                ),
                new OAT\Property(
                    property: 'progress',
                    description: 'Progress of the session as a percentage',
                    type: 'number',
                    format: 'float',
                    example: 5,
                ),
                new OAT\Property(
                    property: 'next_flashcards',
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
                                property: 'language_level',
                                ref: '#/components/schemas/LanguageLevel'
                            ),
                            new OAT\Property(
                                property: 'emoji',
                                description: 'Flashcard context emoji',
                                type: 'string',
                                example: '❤️',
                                nullable: true
                            ),
                        ],
                        type: 'object'
                    )
                ),
            ],
            type: 'object'
        ),
    ]
)]
/**
 * @property SessionFlashcardsRead $resource
 */
class NextSessionFlashcardsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'session' => [
                'id' => $this->resource->getSessionId()->getValue(),
                'cards_per_session' => $this->resource->getCardsPerSession(),
                'is_finished' => $this->resource->getIsFinished(),
                'progress' => $this->resource->getProgress(),
                'next_flashcards' => array_map(function (SessionFlashcardRead $flashcard) {
                    return [
                        'id' => $flashcard->getId()->getValue(),
                        'front_word' => $flashcard->getFrontWord(),
                        'front_lang' => $flashcard->getFrontLang()->getValue(),
                        'back_word' => $flashcard->getBackWord(),
                        'back_lang' => $flashcard->getBackLang()->getValue(),
                        'front_context' => $flashcard->getFrontContext(),
                        'back_context' => $flashcard->getBackContext(),
                        'language_level' => $flashcard->getLanguageLevel()->value,
                        'emoji' => $flashcard->getEmoji(),
                    ];
                }, $this->resource->getSessionFlashcards()),
            ],
        ];
    }
}
