<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropIndexes;

class DropIndexesTest extends TestCase
{
    public function testDropIndexShouldNotAllowEmptyIndexName()
    {
        $this->expectException(InvalidArgumentException::class);
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '');
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '*', $options);
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

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }
}
