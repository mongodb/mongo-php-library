<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\InsertMany;

class InsertManyTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorDocumentsMustNotBeEmpty()
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array());
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorDocumentsMustBeAList()
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array(1 => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorDocumentsElementType($document)
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array($document));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidBooleanArguments
     */
    public function testConstructorOrderedOptionType($ordered)
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array(array('x' => 1)), array('ordered' => $ordered));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     */
    public function testConstructorWriteConcernOptionType()
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array(array('x' => 1)), array('writeConcern' => null));
    }
}
