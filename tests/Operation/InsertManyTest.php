<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\InsertMany;

class InsertManyTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $documents is empty
     */
    public function testConstructorDocumentsMustNotBeEmpty()
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array());
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $documents is not a list (unexpected index: "1")
     */
    public function testConstructorDocumentsMustBeAList()
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array(1 => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$documents\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorDocumentsArgumentElementTypeChecks($document)
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array($document));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), array(array('x' => 1)), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('ordered' => $value);
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = array('writeConcern' => $value);
        }

        return $options;
    }
}
