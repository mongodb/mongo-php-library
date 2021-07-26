<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\RenameCollection;

class RenameCollectionTest extends TestCase
{
    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RenameCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $this->getDatabaseName(),
            $this->getCollectionName() . '.renamed',
            $options
        );
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['dropTarget' => $value];
        }

        return $options;
    }
}
