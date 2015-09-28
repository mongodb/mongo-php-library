<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Count;

class CountTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new Count($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Count($this->getDatabaseName(), $this->getCollectionName(), array(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidHintValues() as $value) {
            $options[][] = array('hint' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('limit' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('maxTimeMS' => $value);
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = array('readPreference' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('skip' => $value);
        }

        return $options;
    }

    private function getInvalidHintValues()
    {
        return array(123, 3.14, true);
    }
}
