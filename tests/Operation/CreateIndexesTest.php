<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateIndexes;

class CreateIndexesTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testCreateIndexesRequiresAtLeastOneIndex()
    {
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    /**
     * @expectedException MongoDB\Exception\UnexpectedTypeException
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
