<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\ReplaceOne;

class ReplaceOneTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['y' => 1]);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorReplacementArgumentTypeCheck($replacement)
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $replacement argument is an update operator
     */
    public function testConstructorReplacementArgumentRequiresNoOperators()
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['$set' => ['x' => 1]]);
    }
}
