<?php

declare(strict_types=1);

namespace Flashcard\Domain\Types;

use Shared\Utils\Types\CollectionType;
use Shared\Utils\Types\TypedCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;

/**
 * @extends TypedCollection<FlashcardId>
 */
#[CollectionType(FlashcardId::class)]
class FlashcardIdCollection extends TypedCollection {}
