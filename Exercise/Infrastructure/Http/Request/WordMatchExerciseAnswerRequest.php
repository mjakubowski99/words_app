<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\Answer\WordMatchAnswer;

#[OAT\Schema(
    schema: 'Requests\Exercise\WordMatchExerciseAnswerRequest',
    description: 'Request schema for answering a word match exercise.',
    required: ['answers'],
    properties: [
        new OAT\Property(
            property: 'answers',
            description: 'Array of word match answers.',
            type: 'array',
            items: new OAT\Items(
                required: ['exercise_entry_id', 'answer'],
                properties: [
                    new OAT\Property(
                        property: 'exercise_entry_id',
                        description: 'ID of the exercise entry being answered.',
                        type: 'integer',
                        example: 123
                    ),
                    new OAT\Property(
                        property: 'answer',
                        description: 'The answer provided for this entry.',
                        type: 'string',
                        maxLength: 255,
                        example: 'matched word'
                    ),
                ],
                type: 'object'
            )
        ),
    ],
    type: 'object'
)]
class WordMatchExerciseAnswerRequest extends Request
{
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*.exercise_entry_id' => ['required', 'integer'],
            'answers.*.answer' => ['required', 'string', 'max:255'],
        ];
    }

    public function getExerciseId(): ExerciseId
    {
        return new ExerciseId((int) $this->route('exercise_id'));
    }

    public function getAnswers(): array
    {
        $answers = [];
        foreach ($this->input('answers') as $request_answer) {
            $answers[] = new WordMatchAnswer(
                new ExerciseEntryId($request_answer['exercise_entry_id']),
                $request_answer['answer']
            );
        }

        return $answers;
    }
}
