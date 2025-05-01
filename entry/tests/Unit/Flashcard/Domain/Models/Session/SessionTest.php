<?php

namespace Tests\Unit\Flashcard\Domain\Models\Session;

use Flashcard\Domain\Models\Session;
use Ramsey\Uuid\Uuid;
use Shared\Enum\ExerciseType;
use Shared\Enum\SessionType;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Tests\TestCase;

class SessionTest extends TestCase
{
    private Session $session;

    /** @dataProvider exerciseTypeProvider */
    public function test__resolveExerciseType_WhenNotMixedType_success(SessionType $type, ?ExerciseType $expected_type): void
    {
        // GIVEN
        $this->session = new Session(
            SessionStatus::STARTED,
            $type,
            new UserId(Uuid::uuid4()->toString()),
            10,
            'Mozilla/Firefox',
            null,
        );

        // WHEN
        $result = $this->session->resolveExerciseType();

        // THEN
        $this->assertSame($result, $expected_type);
    }

    public function test__resolveExerciseType_WhenMixedType_randomChoice(): void
    {
        // GIVEN
        $this->session = new Session(
            SessionStatus::STARTED,
            SessionType::MIXED,
            new UserId(Uuid::uuid4()->toString()),
            10,
            'Mozilla/Firefox',
            null,
        );

        // WHEN
        $result = $this->session->resolveExerciseType();

        // THEN
        $this->assertTrue(in_array($result, [ExerciseType::UNSCRAMBLE_WORDS, null]));
    }

    public static function exerciseTypeProvider(): array
    {
        return [
            ['type' => SessionType::FLASHCARD, 'expected_type' => null],
            ['type' => SessionType::UNSCRAMBLE_WORDS, 'expected_type' => ExerciseType::UNSCRAMBLE_WORDS],
        ];
    }
}
