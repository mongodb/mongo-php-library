<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\InsertOne;

class InsertOneTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorDocumentArgumentType($document)
    {
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     */
    public function testConstructorWriteConcernOptionType()
    {
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('writeConcern' => null));
    }
}
