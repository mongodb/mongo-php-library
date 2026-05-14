<?php

namespace MongoDB\Tests\StreamProcessing\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\StreamProcessing\CreateStreamProcessor;
use MongoDB\Tests\Operation\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CreateStreamProcessorTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateStreamProcessor('proc', [], $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'dlq' => self::getInvalidDocumentValues(),
            'streamMetaFieldName' => self::getInvalidStringValues(),
            'tier' => self::getInvalidStringValues(),
            'failover' => self::getInvalidBooleanValues(),
        ]);
    }
}
