<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Aggregate;

class AggregateTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $pipeline is empty
     */
    public function testConstructorPipelineArgumentMustNotBeEmpty()
    {
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), array());
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $pipeline is not a list (unexpected index: "1")
     */
    public function testConstructorPipelineArgumentMustBeAList()
    {
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), array(1 => array('$match' => array('x' => 1))));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), array(array('$match' => array('x' => 1))), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('allowDiskUse' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('batchSize' => $value);
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = array('maxTimeMS' => $value);
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('useCursor' => $value);
        }

        return $options;
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage "batchSize" option should not be used if "useCursor" is false
     */
    public function testConstructorBatchSizeOptionRequiresUseCursor()
    {
        new Aggregate(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            array(array('$match' => array('x' => 1))),
            array('batchSize' => 100, 'useCursor' => false)
        );
    }
}
