<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Watch;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'batchSize' => self::getInvalidIntegerValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'collation' => self::getInvalidDocumentValues(),
            'fullDocument' => self::getInvalidStringValues(true),
            'fullDocumentBeforeChange' => self::getInvalidStringValues(),
            'maxAwaitTimeMS' => self::getInvalidIntegerValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(true),
            'resumeAfter' => self::getInvalidDocumentValues(),
            'session' => self::getInvalidSessionValues(),
            'startAfter' => self::getInvalidDocumentValues(),
            'startAtOperationTime' => self::getInvalidTimestampValues(),
            'typeMap' => self::getInvalidArrayValues(),
        ]);
    }

    public function testConstructorRejectsCodecAndTypemap(): void
    {
        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());

        $options = ['codec' => new TestDocumentCodec(), 'typeMap' => ['root' => 'array']];
        new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    private static function getInvalidTimestampValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass()];
    }
}
