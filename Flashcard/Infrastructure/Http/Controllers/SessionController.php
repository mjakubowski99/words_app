<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers;

use Flashcard\Application\Command\FlashcardRating;
use Flashcard\Application\Command\RateFlashcards;
use Flashcard\Application\Command\RateFlashcardsCommand;
use Flashcard\Domain\Models\SessionId;
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
    public function get(
        AddSessionFlashcardsHandler $add_session_flashcards,
        GetSessionHandler $get_session,
        GetUnratedSessionFlashcardsHandler $get_unrated_session_flashcards,
    ): SessionFlashcardsResource|JsonResponse {

        $session = $get_session->handle($request->getSessionId());

        $add_session_flashcards->handle(
            new AddSessionFlashcards($session->getId(), $request->getFlashcardsLimit())
        );

        return new SessionFlashcardsResource([
            'session' => $session,
            'flashcards' => $get_unrated_session_flashcards->handle($result->getId(), $request->getFlashcardsLimit()),
        ]);
    }

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

    public function rate(
        RateFlashcards $rate,
        GetSessionHandler $get_session,
        AddSessionFlashcardsHandler $add_session_flashcards,
        GetUnratedSessionFlashcardsHandler $get_unrated_session_flashcards,
    )
    {
        $command = new RateFlashcardsCommand(
            new SessionId(),
            [new FlashcardRating()]
        );

        $rate->handle($command);

        $session = $get_session->handle($command->getSessionId());

        $add_session_flashcards->handle(
            new AddSessionFlashcards($session->getId(), 5)
        );

        return new SessionFlashcardsResource([
            'session' => $session,
            'flashcards' => $get_unrated_session_flashcards->handle($session->getId(), 5),
        ]);
    }
}
