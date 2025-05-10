<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Flashcard\Application\Command\RateFlashcards;
use Flashcard\Application\Command\AddSessionFlashcards;
use Flashcard\Application\Command\CreateSessionHandler;
use Flashcard\Application\Command\RefreshFlashcardPoll;
use Flashcard\Application\Command\RateFlashcardsHandler;
use Flashcard\Application\Command\AddSessionFlashcardsHandler;
use Flashcard\Infrastructure\Http\Request\v2\GetSessionRequest;
use Flashcard\Infrastructure\Http\Request\v2\CreateSessionRequest;
use Flashcard\Infrastructure\Http\Request\v2\RateSessionFlashcardRequest;
use Flashcard\Infrastructure\Http\Resources\v2\NextSessionFlashcardsResource;
use Flashcard\Infrastructure\Http\Factories\NextSessionFlashcardResourceFactory;

class SessionController extends Controller
{
    public const int FLASHCARDS_LIMIT = 1;
    public const int DISPLAY_LIMIT = 1;

    public function get(
        GetSessionRequest $request,
        AddSessionFlashcardsHandler $add_session_flashcards,
        NextSessionFlashcardResourceFactory $factory,
    ): JsonResponse|NextSessionFlashcardsResource {
        DB::transaction(function () use ($request, $add_session_flashcards) {
            $add_session_flashcards->handle(
                new AddSessionFlashcards($request->getSessionId(), $request->currentId(), self::FLASHCARDS_LIMIT)
            );
        });

        return $factory->make($request->getSessionId(), self::FLASHCARDS_LIMIT);
    }

    #[OAT\Post(
        path: '/api/v2/flashcards/session',
        operationId: 'v2.flashcard.session.store',
        description: 'Creates flashcard learning session for given deck',
        summary: 'Creates flashcard learning session for given deck',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\v2\CreateSessionRequest')
        ),
        tags: [Tags::V2, Tags::FLASHCARD],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\NextSessionFlashcardsResource'),
                    ),
                ]),
            ),
            new OAT\Response(
                response: 401,
                description: 'bad request',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Unauthenticated'
                    ),
                ]),
            ),
        ],
    )]
    public function store(
        CreateSessionRequest $request,
        CreateSessionHandler $create_session,
        AddSessionFlashcardsHandler $add_session_flashcards,
        RefreshFlashcardPoll $refresh_flashcard_poll,
        NextSessionFlashcardResourceFactory $factory,
    ): JsonResponse|NextSessionFlashcardsResource {
        $result = $create_session->handle($request->toCommand());

        if (!$request->toCommand()->hasDeckId()) {
            $refresh_flashcard_poll->refresh($request->currentId());
        }

        if (!$result->success()) {
            return new JsonResponse(['message' => $result->getFailReason()], 400);
        }

        DB::transaction(function () use ($add_session_flashcards, $request, $result){
            $add_session_flashcards->handle(
                new AddSessionFlashcards($result->getId(), $request->currentId(), self::FLASHCARDS_LIMIT)
            );
        });

        return $factory->make($result->getId(), self::FLASHCARDS_LIMIT);
    }

    #[OAT\Put(
        path: '/api/v2/flashcards/session/{session_id}/rate-flashcards',
        operationId: 'v2.flashcard.session.rate',
        description: 'Rate session flashcards and get next flashcards to learn',
        summary: 'Rate session flashcards',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\v2\RateSessionFlashcardRequest')
        ),
        tags: [Tags::V2, Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'session_id',
                description: 'Session id',
                in: 'path',
                example: 1,
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\NextSessionFlashcardsResource'),
                    ),
                ]),
            ),
            new OAT\Response(
                response: 401,
                description: 'bad request',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Unauthenticated'
                    ),
                ]),
            ),
        ],
    )]
    public function rate(
        RateSessionFlashcardRequest $request,
        RateFlashcardsHandler $rate,
        AddSessionFlashcardsHandler $add_session_flashcards,
        NextSessionFlashcardResourceFactory $factory,
    ): NextSessionFlashcardsResource {
        $rate_command = new RateFlashcards($request->currentId(), $request->getSessionId(), $request->getRatings());

        $rate->handle($rate_command);

        $add_session_flashcards_command = new AddSessionFlashcards($request->getSessionId(), $request->currentId(), self::FLASHCARDS_LIMIT);

        DB::transaction(function () use ($add_session_flashcards, $add_session_flashcards_command) {
            $add_session_flashcards->handle($add_session_flashcards_command, self::DISPLAY_LIMIT);
        });

        return $factory->make($request->getSessionId(), self::FLASHCARDS_LIMIT);
    }
}
