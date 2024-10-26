<?php

declare(strict_types=1);

namespace Shared\Enum;

enum LearningSessionType: string
{
    case LEARN_FLASHCARDS_IN_CATEGORY = 'learn_flashcards_in_category';
    case LEARN_YOUR_ALL_FLASHCARDS = 'learn_your_all_flashcards';
}
