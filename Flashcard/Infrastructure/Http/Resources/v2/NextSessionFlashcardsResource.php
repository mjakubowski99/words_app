<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use Shared\Enum\Language;
use Shared\Enum\ExerciseType;
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
                    property: 'is_exercise_mode',
                    description: 'This property tells if at the current step exercise should be displayed. '
                    . 'If its false it means that we should display flashcard. '
                    . 'In some modes this is always false.'
                    . 'If flag is true it basically means that we should display exercise from next_exercises property.',
                    type: 'boolean'
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
                            new OAT\Property(
                                property: 'owner_type',
                                ref: '#/components/schemas/FlashcardOwnerType'
                            ),
                        ],
                        type: 'object'
                    )
                ),
                new OAT\Property(
                    property: 'next_exercises',
                    description: 'List of exercises in the session. Type of "data" depends on "exercise_type".',
                    type: 'array',
                    items: new OAT\Items(
                        type: 'object',
                        oneOf: [
                            new OAT\Schema(
                                ref: '#/components/schemas/Resources\Flashcard\v2\UnscrambleWordExerciseResource',
                                discriminator: new OAT\Discriminator(
                                    propertyName: 'exercise_type',
                                    mapping: [
                                        ExerciseType::UNSCRAMBLE_WORDS->value => '#/components/schemas/UnscrambleWordExerciseResource',
                                    ]
                                )
                            ),
                            // new OAT\Schema(ref: '#/components/schemas/NextType')
                        ]
                    )
                ),
            ],
            type: 'object'
        ),
    ]
)]
class NextSessionFlashcardsResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var SessionFlashcardsRead $resource */
        $resource = $this->resource['flashcards'];

        /** @var array $exercises */
        $exercises = $this->resource['exercises'];

        $session_id = $resource->getSessionId()->getValue();

        return [
            'session' => [
                'id' => $session_id,
                'cards_per_session' => $resource->getCardsPerSession(),
                'is_finished' => $resource->getIsFinished(),
                'progress' => $resource->getProgress(),
                'is_exercise_mode' => empty($resource->getSessionFlashcards()) && !empty($exercises),
                'next_flashcards' => array_map(function (SessionFlashcardRead $flashcard) use ($session_id) {
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
                        'owner_type' => $flashcard->getOwnerType()->value,
                        'links' => [
                            'rate' => route('v2.flashcards.session.rate', ['session_id' => $session_id]),
                        ],
                    ];
                }, $resource->getSessionFlashcards()),
                'next_exercises' => array_map(function (array $exercise) {
                    return [
                        'exercise_type' => $exercise['type'],
                        'links' => $exercise['links'],
                        'data' => $exercise['resource'],
                    ];
                }, $exercises),
            ],
        ];
    }
}
