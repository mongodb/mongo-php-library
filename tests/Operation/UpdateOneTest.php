<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\UpdateOne;

class UpdateOneTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorFilterArgumentType($filter)
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), $filter, array('$set' => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorUpdateArgumentType($update)
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), $update);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorUpdateArgumentRequiresOperators()
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('y' => 1));
    }
}
