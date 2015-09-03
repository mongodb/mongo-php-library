<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Find;
use stdClass;

class FindTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new Find($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Find($this->getDatabaseName(), $this->getCollectionName(), array(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('allowPartialResults' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('batchSize' => $value);
        }

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = array('comment' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('cursorType' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('limit' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('maxTimeMS' => $value);
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = array('modifiers' => $value);
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('oplogReplay' => $value);
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = array('projection' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('skip' => $value);
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = array('sort' => $value);
        }

        return $options;
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidConstructorCursorTypeOptions
     */
    public function testConstructorCursorTypeOption($cursorType)
    {
        new Find($this->getDatabaseName(), $this->getCollectionName(), array(), array('cursorType' => $cursorType));
    }

    public function provideInvalidConstructorCursorTypeOptions()
    {
        return $this->wrapValuesForDataProvider(array(-1, 0, 4));
    }
}
