<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Application\Query\GetDeckDetails;
use Flashcard\Application\Query\GetUserCategories;
use Flashcard\Application\Command\GenerateFlashcardsHandler;
use Flashcard\Application\Command\MergeFlashcardDecksHandler;
use Flashcard\Infrastructure\Http\Request\v2\GetDeckDetailsRequest;
use Flashcard\Infrastructure\Http\Resources\v2\DeckDetailsResource;
use Flashcard\Infrastructure\Http\Request\v2\MergeFlashcardsRequest;
use Flashcard\Infrastructure\Http\Resources\v2\FlashcardDecksResource;
use Flashcard\Infrastructure\Http\Request\v2\GenerateFlashcardsRequest;
use Flashcard\Infrastructure\Http\Request\v2\IndexFlashcardDeckRequest;
use Flashcard\Application\Command\RegenerateAdditionalFlashcardsHandler;
use Flashcard\Infrastructure\Http\Request\v2\RegenerateFlashcardsRequest;

class FlashcardDeckController
{
    #[OAT\Get(
        path: '/api/v2/flashcards/decks/by-user',
        operationId: 'v2.flashcards.decks.by-user',
        description: 'Get user flashcard decks',
        summary: 'Get user flashcard decks',
        security: [['sanctum' => []]],
        tags: [Tags::V2, Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'search',
                description: 'Search flashcards parameter',
                in: 'query',
                example: 'Apple',
            ),
            new OAT\Parameter(
                name: 'page',
                description: 'Decks page number',
                in: 'query',
                example: 1,
            ),
            new OAT\Parameter(
                name: 'per_page',
                description: 'Decks per page',
                in: 'query',
                example: 15,
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
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\FlashcardDecksResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function index(
        IndexFlashcardDeckRequest $request,
        GetUserCategories $get_user_decks,
    ): FlashcardDecksResource {
        return new FlashcardDecksResource([
            'decks' => $get_user_decks->handle(
                new Owner(new OwnerId($request->getUserId()->getValue()), FlashcardOwnerType::USER),
                $request->getSearch(),
                $request->getPage(),
                $request->getPerPage(),
            ),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]);
    }

    #[OAT\Post(
        path: '/api/v2/flashcards/decks/generate-flashcards',
        operationId: 'v2.flashcards.generate-by-category',
        description: 'Generate flashcards by provided category',
        summary: 'Generate flashcards by provided category',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\v2\GenerateFlashcardsRequest')
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
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\DeckDetailsResource'),
                    ),
                    new OAT\Property(
                        property: 'merged_to_existing_deck',
                        description: "Variable determinate if generated flashcards are merged to existing deck or it's brand new deck",
                        type: 'bool',
                        example: false
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function generateFlashcards(
        GenerateFlashcardsRequest $request,
        GenerateFlashcardsHandler $generate_flashcards,
        GetDeckDetails $get_deck_details,
    ): DeckDetailsResource {
        $result = $generate_flashcards->handle($request->toCommand());

        return (new DeckDetailsResource([
            'details' => $get_deck_details->get($result->getDeckId(), null, $request->getPage(), $request->getPerPage()),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]))->additional([
            'merged_to_existing_deck' => $result->getMergedToExistingDeck(),
        ]);
    }

    #[OAT\Post(
        path: '/api/v2/flashcards/decks/{flashcard_deck_id}/generate-flashcards',
        operationId: 'v2.flashcards.decks.deck.generate-flashcards',
        description: 'Generate flashcards for existing deck',
        summary: 'Generate flashcards for provided category',
        security: [['sanctum' => []]],
        tags: [Tags::V2, Tags::FLASHCARD],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\DeckDetailsResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function regenerateFlashcards(
        RegenerateFlashcardsRequest $request,
        RegenerateAdditionalFlashcardsHandler $regenerate_flashcards,
        GetDeckDetails $get_deck_details,
    ): DeckDetailsResource {
        $regenerate_flashcards->handle($request->getOwner(), $request->getDeckId());

        return new DeckDetailsResource([
            'details' => $get_deck_details->get($request->getDeckId(), null, $request->getPage(), $request->getPerPage()),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]);
    }

    #[OAT\Post(
        path: '/api/v2/flashcards/decks/{from_deck_id}/merge-flashcards/{to_deck_id}',
        operationId: 'v2.flashcards.decks.merge-flashcards',
        description: 'Merge flashcards',
        summary: 'Merge flashcards',
        security: [['sanctum' => []]],
        tags: [Tags::V2, Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'from_deck_id',
                description: 'Deck which will be merged and deleted',
                in: 'path',
                example: '1',
            ),
            new OAT\Parameter(
                name: 'to_deck_id',
                description: 'Deck to which flashcards from from_deck_id will be merged',
                in: 'path',
                example: '2',
            ),
        ],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function merge(
        MergeFlashcardsRequest $request,
        MergeFlashcardDecksHandler $merge_handler
    ): JsonResponse {
        $merge_handler->handle(
            $request->getOwner(),
            $request->getFromDeckId(),
            $request->getToDeckId(),
            $request->getNewName()
        );

        return new JsonResponse([], 204);
    }

    #[OAT\Get(
        path: '/api/v2/flashcards/decks/{flashcard_deck_id}',
        operationId: 'v2.flashcards.decks.get',
        description: 'Get flashcard details for deck',
        summary: 'Get flashcards and deck details',
        security: [['sanctum' => []]],
        tags: [Tags::V2, Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'search',
                description: 'Search flashcards parameter',
                in: 'query',
                example: 'Apple',
            ),
            new OAT\Parameter(
                name: 'page',
                description: 'Decks page number',
                in: 'query',
                example: 1,
            ),
            new OAT\Parameter(
                name: 'per_page',
                description: 'Decks per page',
                in: 'query',
                example: 15,
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
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\DeckDetailsResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function get(
        GetDeckDetailsRequest $request,
        GetDeckDetails $get_deck_details,
    ): DeckDetailsResource {
        return new DeckDetailsResource([
            'details' => $get_deck_details->get(
                $request->getDeckId(),
                $request->getSearch(),
                $request->getPage(),
                $request->getPerPage()
            ),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]);
    }
}
