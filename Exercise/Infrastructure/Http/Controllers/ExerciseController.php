<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\OpenApi\Tags;
use Exercise\Application\Command\AnswerExercise\UnscrambleWordExerciseAnswerHandler;
use Exercise\Application\Command\SkipExercise\SkipUnscrambleWordExerciseHandler;
use Exercise\Infrastructure\Http\Request\SkipUnscrambleWordExerciseRequest;
use Exercise\Infrastructure\Http\Request\UnscrambleWordExerciseAnswerRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class ExerciseController extends Controller
{
    #[OAT\Put(
        path: '/api/v2/exercises/unscramble-words/{exercise_entry_id}/answer',
        operationId: 'v2.exercises.unscramble-words.answer',
        description: 'Submit an answer for an unscramble word exercise.',
        summary: 'Answer unscramble word exercise',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Exercise\UnscrambleWordExerciseAnswerRequest')
        ),
        tags: [Tags::V2, Tags::EXERCISE],
        responses: [
            new OAT\Response(
                response: 204,
                description: 'Answer is correct, no content returned.'
            ),
            new OAT\Response(
                response: 400,
                description: 'Answer is incorrect.',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Incorrect answer'
                    ),
                ]),
            ),
            new OAT\Response(
                response: 403,
                description: 'Not authorized.'
            ),
        ],
    )]
    public function answerUnscrambleWordExercise(
        UnscrambleWordExerciseAnswerRequest $request,
        UnscrambleWordExerciseAnswerHandler $handler
    ): JsonResponse {
        $assessment = $handler->handle($request->getExerciseEntryId(), $request->currentId(), $request->getAnswer());

        if (!$assessment->isCorrect()) {
            return new JsonResponse([
                'message' => 'Incorrect answer',
            ], 400);
        }

        return new JsonResponse([], 204);
    }

    #[OAT\Put(
        path: '/api/v2/exercises/unscramble-words/{exercise_id}/skip',
        operationId: 'v2.exercises.unscramble-words.skip',
        description: 'Skip an unscramble word exercise.',
        summary: 'Skip unscramble word exercise',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Exercise\SkipUnscrambleWordExerciseRequest')
        ),
        tags: [Tags::V2, Tags::EXERCISE],
        responses: [
            new OAT\Response(
                response: 204,
                description: 'Exercise skipped successfully.'
            ),
            new OAT\Response(
                response: 403,
                description: 'Not authorized.'
            ),
        ],
    )]
    public function skipUnscrambleWordExercise(
        SkipUnscrambleWordExerciseRequest $request,
        SkipUnscrambleWordExerciseHandler $handler
    ): JsonResponse {
        $handler->handle($request->getExerciseId(), $request->currentId());

        return new JsonResponse([], 204);
    }
}
