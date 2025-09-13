<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use Illuminate\Support\Arr;
use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Exercise\Exercises\IWordMatchExerciseReadEntry;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\WordMatchExerciseResource',
    description: 'Resource for word match exercise',
    properties: [
        new OAT\Property(
            property: 'id',
            description: 'ID of the current exercise entry',
            type: 'integer',
            example: 456
        ),
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
        new OAT\Property(
            property: 'sentence_part_before_word',
            description: 'Part of the sentence before the word',
            type: 'string',
            example: 'The '
        ),
        new OAT\Property(
            property: 'sentence_part_after_word',
            description: 'Part of the sentence after the word',
            type: 'string',
            example: ' sits on the mat.'
        ),
        new OAT\Property(
            property: 'answer_options',
            description: 'Array of possible answers for the exercise',
            type: 'array',
            items: new OAT\Items(
                type: 'string',
                example: 'option1'
            )
        ),
    ],
    type: 'object'
)]
/**
 * @property IWordMatchExerciseRead $resource
 */
class WordMatchExerciseResource extends JsonResource
{
    public function toArray($request): ?array
    {
        $entry = Arr::first($this->resource->getEntries(), fn (IWordMatchExerciseReadEntry $entry) => !$entry->isAnswered());

        if (!$entry) {
            return null;
        }

        return [
            'id' => $entry->getExerciseEntryId()->getValue(),
            'exercise_id' => $this->resource->getExerciseId()->getValue(),
            'is_story' => $this->resource->isStory(),
            'word' => $entry->getWord(),
            'word_translation' => $entry->getWordTranslation(),
            'sentence' => $entry->getSentence(),
            'sentence_part_before_word' => $entry->getSentencePartBeforeWord(),
            'sentence_part_after_word' => $entry->getSentencePartAfterWord(),
            'answer_options' => $this->resource->getAnswerOptions(),
        ];
    }
}
