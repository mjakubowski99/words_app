<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModelNotFoundException extends NotFoundHttpException
{
    public function __construct(string $class, string $id)
    {
        $message = "Model {$class} with id: {$id} not found exception";
        parent::__construct($message, null, 404);
    }
}
