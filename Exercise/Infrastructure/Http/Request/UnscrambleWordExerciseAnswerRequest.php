<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordAnswer;

#[OAT\Schema(
    schema: 'Requests\Exercise\UnscrambleWordExerciseAnswerRequest',
    description: 'Request schema for answering an unscramble word exercise.',
    required: ['answer'],
    properties: [
        new OAT\Property(
            property: 'answer',
            description: 'The answer provided for the unscramble word exercise.',
            type: 'string',
            maxLength: 255
        ),
    ],
    type: 'object'
)]
class UnscrambleWordExerciseAnswerRequest extends Request
{
    public function rules(): array
    {
        return [
            'answer' => ['required', 'string', 'max:255'],
        ];
    }

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return new ExerciseEntryId((int) $this->route('exercise_entry_id'));
    }

    public function getAnswer(): UnscrambleWordAnswer
    {
        return new UnscrambleWordAnswer($this->getExerciseEntryId(), $this->input('answer'));
    }
}
