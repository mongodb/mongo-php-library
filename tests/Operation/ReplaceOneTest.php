<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\ReplaceOne;

class ReplaceOneTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorFilterArgumentType($filter)
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), $filter, array('y' => 1));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorReplacementArgumentType($replacement)
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), $replacement);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorReplacementArgumentRequiresNoOperators()
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('$set' => array('x' => 1)));
    }
}
