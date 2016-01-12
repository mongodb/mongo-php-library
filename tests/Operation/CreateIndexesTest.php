<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateIndexes;

class CreateIndexesTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $indexes is empty
     */
    public function testCreateIndexesRequiresAtLeastOneIndex()
    {
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $indexes is not a list (unexpected index: "1")
     */
    public function testConstructorIndexesArgumentMustBeAList()
    {
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [1 => ['key' => ['x' => 1]]]);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidIndexSpecificationTypes
     */
    public function testCreateIndexesRequiresIndexSpecificationsToBeAnArray($index)
    {
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [$index]);
    }

    public function provideInvalidIndexSpecificationTypes()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidArrayValues());
    }
}
