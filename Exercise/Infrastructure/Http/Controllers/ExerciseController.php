<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Http\Controllers;

use App\Http\OpenApi\Tags;
use Exercise\Application\Command\AnswerExercise\WordMatchExerciseAnswerHandler;
use Exercise\Application\Command\SkipExercise\SkipWordMatchExerciseHandler;
use Exercise\Infrastructure\Http\Request\SkipWordMatchExerciseRequest;
use Exercise\Infrastructure\Http\Request\WordMatchExerciseAnswerRequest;
use Exercise\Infrastructure\Http\Resources\WordMatchExerciseAnswerResource;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exercise\Domain\Models\AnswerAssessment;
use Exercise\Infrastructure\Http\Request\SkipUnscrambleWordExerciseRequest;
use Exercise\Infrastructure\Http\Request\UnscrambleWordExerciseAnswerRequest;
use Exercise\Application\Command\SkipExercise\SkipUnscrambleWordExerciseHandler;
use Exercise\Application\Command\AnswerExercise\UnscrambleWordExerciseAnswerHandler;
use Exercise\Infrastructure\Http\Resources\UnscrambleWordExerciseAssessmentResource;

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
                description: 'Answer incorrect',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Exercise\UnscrambleWordExerciseAssessmentResource'),
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
        /** @var AnswerAssessment $assessment */
        $assessments = DB::transaction(function () use ($request, $handler) {
            $exercise_id = $handler->findExerciseId($request->getExerciseEntryId());
            return $handler->handle($exercise_id, $request->currentId(), [$request->getAnswer()]);
        });

        if (!$assessments[0]->isCorrect()) {
            return (new UnscrambleWordExerciseAssessmentResource($assessments[0]))
                ->response()
                ->setStatusCode(400);
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
        DB::transaction(function () use ($request, $handler) {
            $handler->handle($request->getExerciseId(), $request->currentId());
        });

        return new JsonResponse([], 204);
    }

    #[OAT\Put(
        path: '/api/v2/exercises/word-match/{exercise_id}/answer',
        operationId: 'v2.exercises.word-match.answer',
        description: 'Submit an answer for an word match exercise.',
        summary: 'Answer word match exercise',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Exercise\WordMatchExerciseAnswerRequest')
        ),
        tags: [Tags::V2, Tags::EXERCISE],
        responses: [
            new OAT\Response(
                response: 204,
                description: 'Answer is correct, no content returned.'
            ),
            new OAT\Response(
                response: 400,
                description: 'Answer incorrect',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Exercise\UnscrambleWordExerciseAssessmentResource'),
                    ),
                ]),
            ),
            new OAT\Response(
                response: 403,
                description: 'Not authorized.'
            ),
        ],
    )]
    public function answerWordMatchExercise(
        WordMatchExerciseAnswerRequest $request,
        WordMatchExerciseAnswerHandler $handler
    ): WordMatchExerciseAnswerResource {
        /** @var AnswerAssessment[] $assessment */
        $assessments = DB::transaction(function () use ($request, $handler) {
            return $handler->handle($request->getExerciseId(), $request->currentId(), $request->getAnswers());
        });

        return new WordMatchExerciseAnswerResource($assessments);
    }


    #[OAT\Put(
        path: '/api/v2/exercises/word-match/{exercise_id}/skip',
        operationId: 'v2.exercises.word-match.skip',
        description: 'Skip word match exercise.',
        summary: 'Skip word match exercise.',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Exercise\SkipWordMatchExerciseRequest')
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
    public function skipWordMatchExercise(
        SkipWordMatchExerciseRequest $request,
        SkipWordMatchExerciseHandler $handler
    ): JsonResponse {
        DB::transaction(function () use ($request, $handler) {
            $handler->handle($request->getExerciseId(), $request->currentId());
        });

        return new JsonResponse([], 204);
    }
}
