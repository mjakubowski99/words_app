<?php

declare(strict_types=1);

namespace App\Exceptions;

use Flashcard\Domain\Exceptions\RateableSessionFlashcardNotFound;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (\Throwable $e) {});

        $this->renderable(function (RateableSessionFlashcardNotFound $exception, $request) {
            return response()->json(
                [
                    'message' => $exception->getMessage(),
                    'id' => $exception->getIdentifier(),
                ],
                400
            );
        });
    }
}
