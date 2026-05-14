<?php

namespace MongoDB\Tests\StreamProcessing\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\StreamProcessing\StartSampleStreamProcessor;
use MongoDB\Tests\Operation\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class StartSampleStreamProcessorTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StartSampleStreamProcessor('proc', $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'limit' => self::getInvalidIntegerValues(),
        ]);
    }
}
