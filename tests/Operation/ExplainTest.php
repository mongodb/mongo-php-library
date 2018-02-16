<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Count;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\Explain;

class ExplainTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecksForCount(array $options)
    {
        $explainable = new Count($this->getDatabaseName(), $this->getCollectionName(),[]);
        new Explain($this->getDatabaseName(), $explainable, $options);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecksForDistinct(array $options)
    {
        $explainable = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);
        new Explain($this->getDatabaseName(), $explainable, $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = ['verbosity' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
    }
}
