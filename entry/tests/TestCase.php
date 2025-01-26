<?php

declare(strict_types=1);

namespace Tests;

use Mockery\MockInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use TestFactories;

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return MockInterface|T
     */
    public function mockery(string $class): mixed
    {
        return \Mockery::mock($class);
    }

    public function assertArraysAreTheSame(array $a, array $b): void
    {
        sort($a);
        sort($b);

        $this->assertSame($a, $b, 'Failed to assert that two arrays are the same.');
    }
}
