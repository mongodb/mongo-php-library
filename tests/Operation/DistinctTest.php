<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Distinct;

class DistinctTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', $filter);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', array(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('maxTimeMS' => $value);
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = array('readPreference' => $value);
        }

        return $options;
    }
}
