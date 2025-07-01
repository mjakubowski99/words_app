<?php

namespace Exercise\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\ExerciseId;

#[OAT\Schema(
    schema: 'Requests\Exercise\SkipWordMatchExerciseRequest',
    required: ['exercise_id'],
    properties: [
        new OAT\Property(
            property: 'exercise_id',
            description: 'The ID of the exercise to skip.',
            type: 'integer',
            example: 123
        ),
    ],
    type: 'object'
)]
class SkipWordMatchExerciseRequest extends Request
{
    public function getExerciseId(): ExerciseId
    {
        return new ExerciseId((int) $this->route('exercise_id'));
    }
}