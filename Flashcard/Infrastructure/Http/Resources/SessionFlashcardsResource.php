<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use OpenApi\Attributes as OAT;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Application\ReadModels\SessionFlashcardRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\SessionFlashcardsResource',
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
                                property: 'word',
                                description: 'Word on the flashcard',
                                type: 'string',
                                example: 'Polish'
                            ),
                            new OAT\Property(
                                property: 'word_lang',
                                description: 'Language of the word',
                                enum: [Language::EN, Language::PL],
                                example: Language::EN,
                            ),
                            new OAT\Property(
                                property: 'translation',
                                description: 'Translation of the word',
                                type: 'string',
                                example: 'Polska',
                            ),
                            new OAT\Property(
                                property: 'translation_lang',
                                description: 'Language of the translation',
                                type: 'string',
                                enum: [Language::EN, Language::PL],
                                example: Language::PL,
                            ),
                            new OAT\Property(
                                property: 'context',
                                description: 'Context sentence for the word',
                                type: 'string',
                                example: 'Country in the center of Europe'
                            ),
                            new OAT\Property(
                                property: 'context_translation',
                                description: 'Translation of the context sentence',
                                type: 'string',
                                example: 'Kraj w centrum Europy'
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
class SessionFlashcardsResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var SessionRead $session */
        $session = $this->resource['session'];

        /** @var array $flashcards */
        $flashcards = $this->resource['next_flashcards'];

        return [
            'session' => [
                'id' => $session->getId()->getValue(),
                'cards_per_session' => $session->getCardsPerSession(),
                'is_finished' => $session->isFinished(),
                'progress' => $session->getProgress(),
                'next_flashcards' => array_map(function (SessionFlashcardRead $flashcard) {
                    return [
                        'id' => $flashcard->getId()->getValue(),
                        'word' => $flashcard->getWord(),
                        'word_lang' => $flashcard->getWordLang()->getValue(),
                        'translation' => $flashcard->getTranslation(),
                        'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                        'context' => $flashcard->getContext(),
                        'context_translation' => $flashcard->getContextTranslation(),
                    ];
                }, $flashcards),
            ],
        ];
    }
}
