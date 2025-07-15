<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Exercise\Exercises\IWordMatchExerciseReadEntry;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\WordMatchExerciseResource',
    description: 'Resource for word match exercise',
    properties: [
        new OAT\Property(
            property: 'exercise_id',
            description: 'ID of the exercise',
            type: 'integer',
            example: 123
        ),
        new OAT\Property(
            property: 'is_story',
            description: 'Indicates if this is a story-based exercise',
            type: 'boolean',
            example: false
        ),
        new OAT\Property(
            property: 'entries',
            description: 'Array of word match entries',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'id',
                        description: 'ID of the exercise entry',
                        type: 'integer',
                        example: 456
                    ),
                    new OAT\Property(
                        property: 'word',
                        description: 'Word to match',
                        type: 'string',
                        example: 'cat'
                    ),
                    new OAT\Property(
                        property: 'word_translation',
                        description: 'Translation of the word',
                        type: 'string',
                        example: 'kot'
                    ),
                    new OAT\Property(
                        property: 'sentence',
                        description: 'Context sentence for the word',
                        type: 'string',
                        example: 'The cat sits on the mat.'
                    ),
                ]
            )
        ),
    ],
    type: 'object'
)]
/**
 *
 *
 * @property IWordMatchExerciseRead $resource
 */
class WordMatchExerciseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'exercise_id' => $this->resource->getExerciseId()->getValue(),
            'is_story' => $this->resource->isStory(),
            'entries' => array_map(fn(IWordMatchExerciseReadEntry $entry) => [
                'id' => $entry->getExerciseEntryId()->getValue(),
                'word' => $entry->getWord(),
                'word_translation' => $entry->getWordTranslation(),
                'sentence' => $entry->getSentence(),
            ], $this->resource->getEntries()),
        ];
    }
}