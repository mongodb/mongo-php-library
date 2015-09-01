<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Delete;

class DeleteTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorFilterArgumentType($filter)
    {
        new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 0);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorLimitArgumentMustBeOneOrZero()
    {
        new Delete($this->getDatabaseName(), $this->getCollectionName(), array(), 2);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     */
    public function testConstructorWriteConcernOptionType()
    {
        new Delete($this->getDatabaseName(), $this->getCollectionName(), array(), 1, array('writeConcern' => null));
    }
}
