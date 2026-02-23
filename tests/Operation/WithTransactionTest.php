<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\Operation\WithTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use ReflectionProperty;

class WithTransactionTest extends TestCase
{
    public static function provideComputeBackoffMSValues(): Generator
    {
        yield [5, 1];
        yield [7, 2];
        yield [11, 3];
        yield [16, 4];
        yield [25, 5];
        yield [192, 10];
        yield [432, 12];

        // We run into the 500 ms maximum after 13 attempts
        yield [500, 13];
        yield [500, 20];
    }

    #[DataProvider('provideComputeBackoffMSValues')]
    public function testComputeBackoffMS(int $expected, int $attempt): void
    {
        $operation = new WithTransaction(fn () => 0);

        // Set a fixed jitter value instead of a random one
        (new ReflectionProperty($operation, 'jitterGenerator'))
            ->setValue(
                $operation,
                static fn (): float => 1,
            );

        $method = new ReflectionMethod($operation, 'computeBackoffMs');

        $this->assertSame($expected, $method->invoke($operation, $attempt));
    }

    public function testComputeBackoffMSUsesRandom(): void
    {
        $operation = new WithTransaction(fn () => 0);

        $method = new ReflectionMethod($operation, 'computeBackoffMs');
        $first = $method->invoke($operation, 13);
        $second = $method->invoke($operation, 13);

        $this->assertNotSame($first, $second, 'computeBackoffMs() multiplies backoff with a random value');
    }
}
