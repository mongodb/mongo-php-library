<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\Operation\WithTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use ReflectionProperty;

use function array_map;
use function array_unique;
use function count;
use function implode;
use function range;
use function sprintf;

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

        // The same random number can be generated multiple times,
        // but we should get different values across multiple calls
        $method = new ReflectionMethod($operation, 'computeBackoffMs');
        $results = array_map(fn () => $method->invoke($operation, 13), range(0, 5));

        $this->assertGreaterThan(1, count(array_unique($results)), sprintf('Expected random values, got %s', implode(', ', $results)));
    }

    public function testJitter(): void
    {
        $operation = new WithTransaction(fn () => 0);

        $method = new ReflectionMethod($operation, 'getJitter');

        // Test that the default jitter value is between 0 and 1
        for ($i = 0; $i < 100; $i++) {
            $jitter = $method->invoke($operation);
            $this->assertGreaterThanOrEqual(0, $jitter);
            $this->assertLessThanOrEqual(1, $jitter);
        }
    }
}
