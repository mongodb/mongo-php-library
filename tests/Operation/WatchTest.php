<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Watch;

/**
 * Although these are unit tests, we extend FunctionalTestCase because Watch is
 * constructed with a Manager instance.
 */
class WatchTest extends FunctionalTestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $pipeline is not a list (unexpected index: "foo")
     */
    public function testConstructorPipelineArgumentMustBeAList()
    {
        /* Note: Watch uses array_unshift() to prepend the $changeStream stage
         * to the pipeline. Since array_unshift() reindexes numeric keys, we'll
         * use a string key to test for this exception. */
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), ['foo' => ['$match' => ['x' => 1]]]);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['batchSize' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['collation' => $value];
        }

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = ['fullDocument' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxAwaitTimeMS' => $value];
        }

        foreach ($this->getInvalidReadConcernValues() as $value) {
            $options[][] = ['readConcern' => $value];
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['resumeAfter' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
    }
}
