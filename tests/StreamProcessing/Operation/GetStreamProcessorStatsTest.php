<?php

namespace MongoDB\Tests\StreamProcessing\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\StreamProcessing\GetStreamProcessorStats;
use MongoDB\Tests\Operation\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class GetStreamProcessorStatsTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GetStreamProcessorStats('proc', $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'verbose' => self::getInvalidBooleanValues(),
        ]);
    }
}
