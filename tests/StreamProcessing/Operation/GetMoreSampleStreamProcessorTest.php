<?php

namespace MongoDB\Tests\StreamProcessing\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\StreamProcessing\GetMoreSampleStreamProcessor;
use MongoDB\Tests\Operation\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class GetMoreSampleStreamProcessorTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GetMoreSampleStreamProcessor('proc', 123, $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'batchSize' => self::getInvalidIntegerValues(),
        ]);
    }
}
