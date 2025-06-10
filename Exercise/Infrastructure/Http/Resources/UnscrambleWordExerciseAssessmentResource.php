<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Http\Resources;

use OpenApi\Attributes as OAT;
use Exercise\Domain\Models\AnswerAssessment;
use Illuminate\Http\Resources\Json\JsonResource;

#[OAT\Schema(
    schema: 'Resources\Exercise\UnscrambleWordExerciseAssessmentResource',
    description: 'Assessment of user answer in unscramble word exercise',
    properties: [
        new OAT\Property(
            property: 'assessment',
            description: 'Character-by-character assessment of the user input',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'character',
                        description: 'Character provided by the user',
                        type: 'string',
                        example: 'a'
                    ),
                    new OAT\Property(
                        property: 'correct',
                        description: 'Whether the character is correct at this position',
                        type: 'boolean',
                        example: true
                    ),
                ],
                type: 'object'
            )
        ),
        new OAT\Property(
            property: 'user_answer',
            description: 'The complete answer provided by the user',
            type: 'string',
            example: 'tralalal'
        ),
        new OAT\Property(
            property: 'correct_answer',
            description: 'The correct answer for the exercise',
            type: 'string',
            example: 'trxll'
        ),
    ]
)]
/**
 * @property AnswerAssessment $resource
 */
class UnscrambleWordExerciseAssessmentResource extends JsonResource
{
    public function toArray($request): array
    {
        $correct_answer = mb_str_split(trim($this->resource->getCorrectAnswer()));

        $i = -1;

        return [
            'assessment' => array_map(function (string $character) use ($correct_answer, &$i) {
                ++$i;

                return [
                    'character' => $character,
                    'correct' => ($correct_answer[$i] ?? null) === $character,
                ];
            }, mb_str_split(trim($this->resource->getUserAnswer()))),
            'user_answer' => $this->resource->getUserAnswer(),
            'correct_answer' => $this->resource->getCorrectAnswer(),
        ];
    }
}
