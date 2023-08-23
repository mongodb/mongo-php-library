<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Watch;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use stdClass;

/**
 * Although these are unit tests, we extend FunctionalTestCase because Watch is
 * constructed with a Manager instance.
 */
class WatchTest extends FunctionalTestCase
{
    public function testConstructorCollectionNameShouldBeNullIfDatabaseNameIsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$collectionName should also be null if $databaseName is null');

        new Watch($this->manager, null, 'foo', []);
    }

    public function testConstructorPipelineArgumentMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$pipeline is not a valid aggregation pipeline');

        /* Note: Watch uses array_unshift() to prepend the $changeStream stage
         * to the pipeline. Since array_unshift() reindexes numeric keys, we'll
         * use a string key to test for this exception. */
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), ['foo' => ['$match' => ['x' => 1]]]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'batchSize' => $this->getInvalidIntegerValues(),
            'codec' => $this->getInvalidDocumentCodecValues(),
            'collation' => $this->getInvalidDocumentValues(),
            'fullDocument' => $this->getInvalidStringValues(true),
            'fullDocumentBeforeChange' => $this->getInvalidStringValues(),
            'maxAwaitTimeMS' => $this->getInvalidIntegerValues(),
            'readConcern' => $this->getInvalidReadConcernValues(),
            'readPreference' => $this->getInvalidReadPreferenceValues(true),
            'resumeAfter' => $this->getInvalidDocumentValues(),
            'session' => $this->getInvalidSessionValues(),
            'startAfter' => $this->getInvalidDocumentValues(),
            'startAtOperationTime' => $this->getInvalidTimestampValues(),
            'typeMap' => $this->getInvalidArrayValues(),
        ]);
    }

    public function testConstructorRejectsCodecAndTypemap(): void
    {
        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());

        $options = ['codec' => new TestDocumentCodec(), 'typeMap' => ['root' => 'array']];
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    private function getInvalidTimestampValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass()];
    }
}
