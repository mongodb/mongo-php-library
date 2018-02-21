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
    public function testConstructorOptionTypeChecks(array $options)
    {
        $explainable = $this->getMockBuilder('MongoDB\Operation\Explainable')->getMock();
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
