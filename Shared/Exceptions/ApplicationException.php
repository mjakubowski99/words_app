<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as BaseHttpException;

class ApplicationException extends BaseHttpException
{
    protected $message = 'Some error occurred';
    protected $code = Response::HTTP_EXPECTATION_FAILED;
    protected array $headers = [];

    public function __construct(?string $message = null, ?\Throwable $previous = null)
    {
        if ($message) {
            $this->message = $message;
        }

        parent::__construct($this->code, $this->message, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
