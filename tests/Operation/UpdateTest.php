<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Update;

class UpdateTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$filter to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, array('$set' => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$update to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorUpdateArgumentTypeCheck($update)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), $update);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('y' => 1), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('multi' => $value);
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('upsert' => $value);
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = array('writeConcern' => $value);
        }

        return $options;
    }
}
