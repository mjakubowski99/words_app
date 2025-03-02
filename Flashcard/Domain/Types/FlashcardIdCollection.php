<?php

declare(strict_types=1);

namespace Flashcard\Domain\Types;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Utils\Types\CollectionType;
use Shared\Utils\Types\TypedCollection;

/**
 * @extends TypedCollection<FlashcardId>
 */
#[CollectionType(FlashcardId::class)]
class FlashcardIdCollection extends TypedCollection {}