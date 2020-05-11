<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateIndexes;
use stdClass;

class CreateIndexesTest extends TestCase
{
    public function testConstructorIndexesArgumentMustBeAList()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is not a list (unexpected index: "1")');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [1 => ['key' => ['x' => 1]]]);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1]]], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ([3.14, true, [], new stdClass()] as $value) {
            $options[][] = ['commitQuorum' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxTimeMS' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    public function testConstructorRequiresAtLeastOneIndex()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is empty');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    /**
     * @dataProvider provideInvalidIndexSpecificationTypes
     */
    public function testConstructorRequiresIndexSpecificationsToBeAnArray($index)
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [$index]);
    }

    public function provideInvalidIndexSpecificationTypes()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidArrayValues());
    }
}
