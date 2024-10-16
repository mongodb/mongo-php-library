<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\ListSearchIndexes;
use PHPUnit\Framework\Attributes\DataProvider;

class ListSearchIndexesTest extends TestCase
{
    public function testConstructorIndexNameMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), ['name' => '']);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        $options = [];

        foreach (self::getInvalidIntegerValues() as $value) {
            $options[][] = ['batchSize' => $value];
        }

        $options[][] = ['codec' => 'foo'];

        return $options;
    }
}
