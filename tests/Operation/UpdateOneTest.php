<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\UpdateOne;

class UpdateOneTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['$set' => ['x' => 1]]);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorUpdateArgumentTypeCheck($update)
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $update argument is not an update operator
     */
    public function testConstructorUpdateArgumentRequiresOperators()
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1]);
    }
}
