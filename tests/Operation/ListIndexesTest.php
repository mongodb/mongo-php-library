<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\ListIndexes;

class ListIndexesTest extends TestCase
{
    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new ListIndexes($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxTimeMS' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        return $options;
    }
}
