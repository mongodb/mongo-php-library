<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Watch;
use stdClass;

/**
 * Although these are unit tests, we extend FunctionalTestCase because Watch is
 * constructed with a Manager instance.
 */
class WatchTest extends FunctionalTestCase
{
    public function testConstructorCollectionNameShouldBeNullIfDatabaseNameIsNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$collectionName should also be null if $databaseName is null');

        new Watch($this->manager, null, 'foo', []);
    }

    public function testConstructorPipelineArgumentMustBeAList()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$pipeline is not a list (unexpected index: "foo")');

        /* Note: Watch uses array_unshift() to prepend the $changeStream stage
         * to the pipeline. Since array_unshift() reindexes numeric keys, we'll
         * use a string key to test for this exception. */
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), ['foo' => ['$match' => ['x' => 1]]]);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
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

        foreach ($this->getInvalidTimestampValues() as $value) {
            $options[][] = ['startAtOperationTime' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
    }

    private function getInvalidTimestampValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass()];
    }
}
