<?php

namespace Flashcard\Domain\Models;

enum Rating: int
{
    case UNKNOWN = 0;
    case WEAK = 1;
    case GOOD = 2;
    case VERY_GOOD = 3;
}