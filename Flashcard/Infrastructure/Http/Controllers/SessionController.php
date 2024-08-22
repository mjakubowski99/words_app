<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Flashcard\Application\Query\GetSessionHandler;
use Flashcard\Application\Command\CreateSessionHandler;
use Flashcard\Application\Query\GetUnratedSessionFlashcardsHandler;
use Flashcard\Application\Command\AddSessionFlashcards;
use Flashcard\Infrastructure\Http\Request\CreateSessionRequest;
use Flashcard\Application\Command\AddSessionFlashcardsHandler;
use Flashcard\Infrastructure\Http\Resources\SessionFlashcardsResource;

class SessionController extends Controller
{
    public function store(
        CreateSessionRequest               $request,
        CreateSessionHandler               $create_session,
        AddSessionFlashcardsHandler        $add_session_flashcards,
        GetSessionHandler                  $get_session,
        GetUnratedSessionFlashcardsHandler $get_unrated_session_flashcards,
    ): SessionFlashcardsResource|JsonResponse {
        $result = $create_session->handle($request->toCommand());

        if (!$result->success()) {
            return new JsonResponse(['message' => $result->getFailReason()], 400);
        }

        $add_session_flashcards->handle(
            new AddSessionFlashcards($result->getId(), $request->getFlashcardsLimit())
        );

        return new SessionFlashcardsResource([
            'session' => $get_session->handle($result->getId()),
            'flashcards' => $get_unrated_session_flashcards->handle($result->getId(), $request->getFlashcardsLimit()),
        ]);
    }
}
