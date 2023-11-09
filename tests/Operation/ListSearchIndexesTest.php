<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\ListSearchIndexes;

class ListSearchIndexesTest extends TestCase
{
    public function testConstructorIndexNameMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), ['name' => '']);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions(): array
    {
        $options = [];

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['batchSize' => $value];
        }

        $options[][] = ['codec' => 'foo'];

        return $options;
    }
}
