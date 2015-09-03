<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Delete;

class DeleteTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 0);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $limit must be 0 or 1
     * @dataProvider provideInvalidLimitValues
     */
    public function testConstructorLimitArgumentMustBeOneOrZero($limit)
    {
        new Delete($this->getDatabaseName(), $this->getCollectionName(), array(), $limit);
    }

    public function provideInvalidLimitValues()
    {
        return $this->wrapValuesForDataProvider(array_merge($this->getInvalidIntegerValues(), array(-1, 2)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Delete($this->getDatabaseName(), $this->getCollectionName(), array(), 1, $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = array('writeConcern' => $value);
        }

        return $options;
    }
}
