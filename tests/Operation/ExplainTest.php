<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Explain;
use MongoDB\Operation\Explainable;

class ExplainTest extends TestCase
{
    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $explainable = $this->getMockBuilder(Explainable::class)->getMock();
        $this->expectException(InvalidArgumentException::class);
        new Explain($this->getDatabaseName(), $explainable, $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = ['verbosity' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
    }
}
