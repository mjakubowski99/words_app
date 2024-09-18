<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

use Shared\Exceptions\BadRequestException;

class ActiveSessionAlreadyExistsException extends BadRequestException {}
