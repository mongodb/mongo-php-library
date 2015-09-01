<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\UpdateMany;

class UpdateManyTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorFilterArgumentType($filter)
    {
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), $filter, array('$set' => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorUpdateArgumentType($update)
    {
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), $update);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorUpdateArgumentRequiresOperators()
    {
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('y' => 1));
    }
}
