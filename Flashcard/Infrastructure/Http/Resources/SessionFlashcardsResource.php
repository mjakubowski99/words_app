<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources;

use OpenApi\Attributes as OAT;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Application\DTO\SessionDetailsDTO;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\DTO\SessionFlashcardDTO;
use Flashcard\Application\DTO\SessionFlashcardsDTO;

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
                                property: 'rating',
                                description: 'User rating for the flashcard',
                                type: 'integer',
                                enum: [Rating::UNKNOWN, Rating::WEAK, Rating::GOOD, Rating::VERY_GOOD],
                                example: Rating::GOOD,
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
        /** @var SessionDetailsDTO $session */
        $session = $this->resource['session'];

        /** @var SessionFlashcardsDTO $flashcards */
        $flashcards = $this->resource['flashcards'];

        return [
            'session' => [
                'id' => $session->getId()->getValue(),
                'cards_per_session' => $session->getCardsPerSession(),
                'is_finished' => $session->isFinished(),
                'progress' => $session->getProgress(),
                'flashcards' => array_map(function (SessionFlashcardDTO $flashcard_dto) {
                    return [
                        'id' => $flashcard_dto->getId()->getValue(),
                        'rating' => $flashcard_dto->getRating(),
                        'word' => $flashcard_dto->getWord(),
                        'word_lang' => $flashcard_dto->getWordLang()->getValue(),
                        'translation' => $flashcard_dto->getTranslation(),
                        'translation_lang' => $flashcard_dto->getTranslationLang()->getValue(),
                        'context' => $flashcard_dto->getContext(),
                        'context_translation' => $flashcard_dto->getContextTranslation(),
                    ];
                }, $flashcards->getFlashcards()),
            ],
        ];
    }
}
