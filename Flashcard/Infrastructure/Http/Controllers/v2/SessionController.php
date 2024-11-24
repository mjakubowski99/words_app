<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use App\Http\Controllers\Controller;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Application\Command\RateFlashcards;
use Flashcard\Application\Command\AddSessionFlashcards;
use Flashcard\Application\Command\CreateSessionHandler;
use Flashcard\Application\Command\RateFlashcardsHandler;
use Flashcard\Application\Command\AddSessionFlashcardsHandler;
use Flashcard\Infrastructure\Http\Request\v2\GetSessionRequest;
use Flashcard\Application\Query\GetNextSessionFlashcardsHandler;
use Flashcard\Infrastructure\Http\Request\v2\CreateSessionRequest;
use Flashcard\Infrastructure\Http\Request\v2\RateSessionFlashcardRequest;
use Flashcard\Infrastructure\Http\Resources\v2\NextSessionFlashcardsResource;

class SessionController extends Controller
{
    public const FLASHCARDS_LIMIT = 1;

    public function get(
        GetSessionRequest $request,
        AddSessionFlashcardsHandler $add_session_flashcards,
        GetNextSessionFlashcardsHandler $get_next_session_flashcards,
    ): JsonResponse|NextSessionFlashcardsResource {
        $add_session_flashcards->handle(
            new AddSessionFlashcards($request->getSessionId(), self::FLASHCARDS_LIMIT)
        );

        return new NextSessionFlashcardsResource(
            $get_next_session_flashcards->handle($request->getSessionId(), self::FLASHCARDS_LIMIT)
        );
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
        GetNextSessionFlashcardsHandler $get_next_session_flashcards,
    ): JsonResponse|NextSessionFlashcardsResource {
        $result = $create_session->handle($request->toCommand());

        if (!$result->success()) {
            return new JsonResponse(['message' => $result->getFailReason()], 400);
        }

        $add_session_flashcards->handle(
            new AddSessionFlashcards($result->getId(), self::FLASHCARDS_LIMIT)
        );

        return new NextSessionFlashcardsResource(
            $get_next_session_flashcards->handle($result->getId(), self::FLASHCARDS_LIMIT)
        );
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
        GetNextSessionFlashcardsHandler $get_next_session_flashcards,
    ): NextSessionFlashcardsResource {
        $rate_command = new RateFlashcards(
            new Owner(new OwnerId($request->currentId()->getValue()), FlashcardOwnerType::USER),
            $request->getSessionId(),
            $request->getRatings(),
        );
        $add_session_flashcards_command = new AddSessionFlashcards($request->getSessionId(), self::FLASHCARDS_LIMIT);

        $rate->handle($rate_command);

        $add_session_flashcards->handle($add_session_flashcards_command);

        return new NextSessionFlashcardsResource(
            $get_next_session_flashcards->handle($request->getSessionId(), self::FLASHCARDS_LIMIT)
        );
    }
}
