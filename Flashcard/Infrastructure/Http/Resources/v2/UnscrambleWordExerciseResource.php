<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Resources\v2;

use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;

#[OAT\Schema(
    schema: 'Resources\Flashcard\v2\UnscrambleWordExerciseResource',
    description: 'Resource for unscramble word exercise',
    properties: [
        new OAT\Property(
            property: 'id',
            description: 'ID of the exercise',
            type: 'integer',
            example: 123
        ),
        new OAT\Property(
            property: 'exercise_entry_id',
            description: 'ID of the unscramble word exercise entry',
            type: 'integer',
            example: 12
        ),
        new OAT\Property(
            property: 'front_word',
            description: 'The correct word to be unscrambled',
            type: 'string',
            example: 'Polska'
        ),
        new OAT\Property(
            property: 'context_sentence',
            description: 'Context sentence in which the word is used',
            type: 'string',
            example: 'This country is located in central Europe.'
        ),
        new OAT\Property(
            property: 'emoji',
            description: 'Emoji associated with the exercise',
            type: 'string',
            example: '🇵🇱'
        ),
        new OAT\Property(
            property: 'keyboard',
            description: 'Keyboard characters which should be used to match the word',
            type: 'array',
            items: new OAT\Items(type: 'string'),
            example: ['o', 'l', 's', 'p', 'k', 'a']
        ),
    ],
    type: 'object'
)]
/** @property IUnscrambleWordExerciseRead $resource */
class UnscrambleWordExerciseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId()->getValue(),
            'exercise_entry_id' => $this->resource->getExerciseEntryId(),
            'front_word' => $this->resource->getFrontWord(),
            'context_sentence' => $this->resource->getContextSentence(),
            'emoji' => $this->resource->getEmoji(),
            'keyboard' => $this->resource->getKeyboard(),
        ];
    }
}
