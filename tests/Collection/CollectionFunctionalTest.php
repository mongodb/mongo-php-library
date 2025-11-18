<?php

namespace MongoDB\Tests\Collection;

use Closure;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\Encoder;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Operation\Count;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use TypeError;

use function array_filter;
use function call_user_func;
use function is_scalar;
use function iterator_to_array;
use function json_encode;
use function str_contains;
use function usort;

use const JSON_THROW_ON_ERROR;

/**
 * Functional tests for the Collection class.
 */
class CollectionFunctionalTest extends FunctionalTestCase
{
    #[DataProvider('provideInvalidDatabaseAndCollectionNames')]
    public function testConstructorDatabaseNameArgument($databaseName, string $expectedExceptionClass): void
    {
        $this->expectException($expectedExceptionClass);
        // TODO: Move to unit test once ManagerInterface can be mocked (PHPC-378)
        new Collection($this->manager, $databaseName, $this->getCollectionName());
    }

    #[DataProvider('provideInvalidDatabaseAndCollectionNames')]
    public function testConstructorCollectionNameArgument($collectionName, string $expectedExceptionClass): void
    {
        $this->expectException($expectedExceptionClass);
        // TODO: Move to unit test once ManagerInterface can be mocked (PHPC-378)
        new Collection($this->manager, $this->getDatabaseName(), $collectionName);
    }

    public static function provideInvalidDatabaseAndCollectionNames()
    {
        return [
            [null, TypeError::class],
            ['', InvalidArgumentException::class],
        ];
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'builderEncoder' => self::getInvalidObjectValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testGetBuilderEncoder(): void
    {
        $collectionOptions = ['builderEncoder' => $this->createMock(Encoder::class)];
        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);

        $this->assertSame($collectionOptions['builderEncoder'], $collection->getBuilderEncoder());
    }

    public function testGetCodec(): void
    {
        $collectionOptions = ['codec' => $this->createMock(DocumentCodec::class)];
        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);

        $this->assertSame($collectionOptions['codec'], $collection->getCodec());
    }

    public function testGetManager(): void
    {
        $this->assertSame($this->manager, $this->collection->getManager());
    }

    public function testToString(): void
    {
        $this->assertEquals($this->getNamespace(), (string) $this->collection);
    }

    public function getGetCollectionName(): void
    {
        $this->assertEquals($this->getCollectionName(), $this->collection->getCollectionName());
    }

    public function getGetDatabaseName(): void
    {
        $this->assertEquals($this->getDatabaseName(), $this->collection->getDatabaseName());
    }

    public function testGetNamespace(): void
    {
        $this->assertEquals($this->getNamespace(), $this->collection->getNamespace());
    }

    public function testAggregateWithinTransaction(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        // Collection must be created before the transaction starts
        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $session = $this->manager->startSession();
        $session->startTransaction();

        try {
            $this->createFixtures(3, ['session' => $session]);

            $cursor = $this->collection->aggregate(
                [['$match' => ['_id' => ['$lt' => 3]]]],
                ['session' => $session],
            );

            $expected = [
                ['_id' => 1, 'x' => 11],
                ['_id' => 2, 'x' => 22],
            ];

            $this->assertSameDocuments($expected, $cursor);

            $session->commitTransaction();
        } finally {
            $session->endSession();
        }
    }

    public function testCreateIndexSplitsCommandOptions(): void
    {
        $this->skipIfServerVersion('<', '4.4', 'commitQuorum and comment options are not supported');

        if ($this->isStandalone()) {
            $this->markTestSkipped('commitQuorum is not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $this->collection->createIndex(
                    ['x' => 1],
                    [
                        'comment' => 'foo',
                        'commitQuorum' => 'majority',
                        'maxTimeMS' => 10000,
                        'session' => $this->manager->startSession(),
                        'sparse' => true,
                        'unique' => true,
                        'writeConcern' => new WriteConcern(1),
                    ],
                );
            },
            function (array $event): void {
                $command = $event['started']->getCommand();
                $this->assertObjectHasProperty('comment', $command);
                $this->assertObjectHasProperty('commitQuorum', $command);
                $this->assertObjectHasProperty('lsid', $command);
                $this->assertObjectHasProperty('maxTimeMS', $command);
                $this->assertObjectHasProperty('writeConcern', $command);
                $this->assertObjectHasProperty('sparse', $command->indexes[0]);
                $this->assertObjectHasProperty('unique', $command->indexes[0]);
            },
        );
    }

    #[DataProvider('provideTypeMapOptionsAndExpectedDocuments')]
    public function testDistinctWithTypeMap(array $typeMap, array $expectedDocuments): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);
        $bulkWrite->insert([
            'x' => (object) ['foo' => 'bar'],
        ]);
        $bulkWrite->insert(['x' => 4]);
        $bulkWrite->insert([
            'x' => (object) ['foo' => ['foo' => 'bar']],
        ]);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $values = $this->collection->withOptions(['typeMap' => $typeMap])->distinct('x');

        /* This sort callable sorts all scalars to the front of the list. All
         * non-scalar values are sorted by running json_encode on them and
         * comparing their string representations.
         */
        $sort = function ($a, $b) {
            if (is_scalar($a) && ! is_scalar($b)) {
                return -1;
            }

            if (! is_scalar($a)) {
                if (is_scalar($b)) {
                    return 1;
                }

                $a = json_encode($a, JSON_THROW_ON_ERROR);
                $b = json_encode($b, JSON_THROW_ON_ERROR);
            }

            return $a < $b ? -1 : 1;
        };

        usort($expectedDocuments, $sort);
        usort($values, $sort);

        $this->assertEquals($expectedDocuments, $values);
    }

    public static function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            'No type map' => [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => ['foo' => 'bar']],
                ],
            ],
            'array/array' => [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => ['foo' => 'bar']],
                ],
            ],
            'object/array' => [
                ['root' => 'object', 'document' => 'array'],
                [
                    (object) ['foo' => 'bar'],
                    4,
                    (object) ['foo' => ['foo' => 'bar']],
                ],
            ],
            'array/stdClass' => [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => (object) ['foo' => 'bar']],
                ],
            ],
        ];
    }

    public function testDrop(): void
    {
        $writeResult = $this->collection->insertOne(['x' => 1]);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $this->collection->drop();
        $this->assertCollectionDoesNotExist($this->getCollectionName());
    }

    /** @todo Move this to a unit test once Manager can be mocked */
    public function testDropIndexShouldNotAllowWildcardCharacter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->collection->dropIndex('*');
    }

    public function testExplain(): void
    {
        $this->createFixtures(3);

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);

        $result = $this->collection->explain($operation);

        $this->assertArrayHasKey('queryPlanner', $result);
    }

    public function testFindOne(): void
    {
        $this->createFixtures(5);

        $filter = ['_id' => ['$lt' => 5]];
        $options = [
            'skip' => 1,
            'sort' => ['x' => -1],
        ];

        $expected = ['_id' => 3, 'x' => 33];

        $this->assertSameDocument($expected, $this->collection->findOne($filter, $options));
    }

    public function testFindWithinTransaction(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        // Collection must be created before the transaction starts
        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $session = $this->manager->startSession();
        $session->startTransaction();

        try {
            $this->createFixtures(3, ['session' => $session]);

            $cursor = $this->collection->find(
                ['_id' => ['$lt' => 3]],
                ['session' => $session],
            );

            $expected = [
                ['_id' => 1, 'x' => 11],
                ['_id' => 2, 'x' => 22],
            ];

            $this->assertSameDocuments($expected, $cursor);

            $session->commitTransaction();
        } finally {
            $session->endSession();
        }
    }

    public function testRenameToSameDatabase(): void
    {
        $toCollectionName = $this->getCollectionName() . '.renamed';
        $toCollection = new Collection($this->manager, $this->getDatabaseName(), $toCollectionName);

        $writeResult = $this->collection->insertOne(['_id' => 1]);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $this->collection->rename($toCollectionName, null, ['dropTarget' => true]);
        $this->assertCollectionDoesNotExist($this->getCollectionName());
        $this->assertCollectionExists($toCollectionName);

        $this->assertSameDocument(['_id' => 1], $toCollection->findOne());
        $toCollection->drop();
    }

    public function testRenameToDifferentDatabase(): void
    {
        $toDatabaseName = $this->getDatabaseName() . '_renamed';
        $toDatabase = new Database($this->manager, $toDatabaseName);

        /* When renaming an unsharded collection, mongos requires the source
        * and target database to both exist on the primary shard. In practice,
        * this means we need to create the target database explicitly.
        * See: https://mongodb.com/docs/manual/reference/command/renameCollection/#unsharded-collections
        */
        if ($this->isShardedCluster()) {
            $toDatabase->foo->insertOne(['_id' => 1]);
        }

        $toCollectionName = $this->getCollectionName() . '.renamed';
        $toCollection = new Collection($this->manager, $toDatabaseName, $toCollectionName);

        $writeResult = $this->collection->insertOne(['_id' => 1]);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $this->collection->rename($toCollectionName, $toDatabaseName);
        $this->assertCollectionDoesNotExist($this->getCollectionName());
        $this->assertCollectionExists($toCollectionName, $toDatabaseName);

        $this->assertSameDocument(['_id' => 1], $toCollection->findOne());

        $toDatabase->drop();
    }

    public function testWithOptionsInheritsOptions(): void
    {
        $collectionOptions = [
            'builderEncoder' => $this->createMock(Encoder::class),
            'codec' => $this->createMock(DocumentCodec::class),
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);
        $clone = $collection->withOptions();
        $debug = $clone->__debugInfo();

        $this->assertSame($this->manager, $debug['manager']);
        $this->assertSame($this->getDatabaseName(), $debug['databaseName']);
        $this->assertSame($this->getCollectionName(), $debug['collectionName']);

        foreach ($collectionOptions as $key => $value) {
            $this->assertSame($value, $debug[$key]);
        }

        // autoEncryptionEnabled is an internal option not reported via debug info
        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), ['autoEncryptionEnabled' => true]);
        $clone = $collection->withOptions();

        $rc = new ReflectionClass($clone);
        $rp = $rc->getProperty('autoEncryptionEnabled');

        $this->assertSame(true, $rp->getValue($clone));
    }

    public function testWithOptionsPassesOptions(): void
    {
        $collectionOptions = [
            'builderEncoder' => $this->createMock(Encoder::class),
            'codec' => $this->createMock(DocumentCodec::class),
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $clone = $this->collection->withOptions($collectionOptions);
        $debug = $clone->__debugInfo();

        foreach ($collectionOptions as $key => $value) {
            $this->assertSame($value, $debug[$key]);
        }

        // autoEncryptionEnabled is an internal option not reported via debug info
        $clone = $this->collection->withOptions(['autoEncryptionEnabled' => true]);

        $rc = new ReflectionClass($clone);
        $rp = $rc->getProperty('autoEncryptionEnabled');

        $this->assertSame(true, $rp->getValue($clone));
    }

    public static function collectionMethodClosures()
    {
        return [
            'read-only aggregate' => [
                function ($collection, $session, $options = []): void {
                    $collection->aggregate(
                        [['$match' => ['_id' => ['$lt' => 3]]]],
                        ['session' => $session] + $options,
                    );
                }, 'r',
            ],

            /* Disabled, as write aggregations are not supported in transactions
            'read-write aggregate' => [
                function ($collection, $session, $options = []): void {
                    $collection->aggregate(
                        [
                            ['$match' => ['_id' => ['$lt' => 3]]],
                            ['$merge' => $collection . '_out'],
                        ],
                        ['session' => $session] + $options
                    );
                }, 'rw',
            ],
            */

            'bulkWrite insertOne' => [
                function ($collection, $session, $options = []): void {
                    $collection->bulkWrite(
                        [['insertOne' => [['test' => 'foo']]]],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            /* Disabled, as count command can't be used in transactions
            'count' => [
                function($collection, $session, $options = []) {
                    $collection->count(
                        [],
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */

            'countDocuments' => [
                function ($collection, $session, $options = []): void {
                    $collection->countDocuments(
                        [],
                        ['session' => $session] + $options,
                    );
                }, 'r',
            ],

            /* Disabled, as it's illegal to use createIndex command in transactions
            'createIndex' => [
                function($collection, $session, $options = []) {
                    $collection->createIndex(
                        ['test' => 1],
                        ['session' => $session] + $options
                    );
                }, 'w'
            ],
            */

            'deleteMany' => [
                function ($collection, $session, $options = []): void {
                    $collection->deleteMany(
                        ['test' => 'foo'],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'deleteOne' => [
                function ($collection, $session, $options = []): void {
                    $collection->deleteOne(
                        ['test' => 'foo'],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'distinct' => [
                function ($collection, $session, $options = []): void {
                    $collection->distinct(
                        '_id',
                        [],
                        ['session' => $session] + $options,
                    );
                }, 'r',
            ],

            /* Disabled, as it's illegal to use drop command in transactions
            'drop' => [
                function($collection, $session, $options = []) {
                    $collection->drop(
                        ['session' => $session] + $options
                    );
                }, 'w'
            ],
            */

            /* Disabled, as it's illegal to use dropIndexes command in transactions
            'dropIndex' => [
                function($collection, $session, $options = []) {
                    $collection->dropIndex(
                        '_id_1',
                        ['session' => $session] + $options
                    );
                }, 'w'
            ], */

            /* Disabled, as it's illegal to use dropIndexes command in transactions
            'dropIndexes' => [
                function($collection, $session, $options = []) {
                    $collection->dropIndexes(
                        ['session' => $session] + $options
                    );
                }, 'w'
            ],
            */

            /* Disabled, as count command can't be used in transactions
            'estimatedDocumentCount' => [
                function($collection, $session, $options = []) {
                    $collection->estimatedDocumentCount(
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */

            'find' => [
                function ($collection, $session, $options = []): void {
                    $collection->find(
                        ['test' => 'foo'],
                        ['session' => $session] + $options,
                    );
                }, 'r',
            ],

            'findOne' => [
                function ($collection, $session, $options = []): void {
                    $collection->findOne(
                        ['test' => 'foo'],
                        ['session' => $session] + $options,
                    );
                }, 'r',
            ],

            'findOneAndDelete' => [
                function ($collection, $session, $options = []): void {
                    $collection->findOneAndDelete(
                        ['test' => 'foo'],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'findOneAndReplace' => [
                function ($collection, $session, $options = []): void {
                    $collection->findOneAndReplace(
                        ['test' => 'foo'],
                        [],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'findOneAndUpdate' => [
                function ($collection, $session, $options = []): void {
                    $collection->findOneAndUpdate(
                        ['test' => 'foo'],
                        ['$set' => ['updated' => 1]],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'insertMany' => [
                function ($collection, $session, $options = []): void {
                    $collection->insertMany(
                        [
                            ['test' => 'foo'],
                            ['test' => 'bar'],
                        ],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'insertOne' => [
                function ($collection, $session, $options = []): void {
                    $collection->insertOne(
                        ['test' => 'foo'],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            /* Disabled, as it's illegal to use listIndexes command in transactions
            'listIndexes' => [
                function($collection, $session, $options = []) {
                    $collection->listIndexes(
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */

            'replaceOne' => [
                function ($collection, $session, $options = []): void {
                    $collection->replaceOne(
                        ['test' => 'foo'],
                        [],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'updateMany' => [
                function ($collection, $session, $options = []): void {
                    $collection->updateMany(
                        ['test' => 'foo'],
                        ['$set' => ['updated' => 1]],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            'updateOne' => [
                function ($collection, $session, $options = []): void {
                    $collection->updateOne(
                        ['test' => 'foo'],
                        ['$set' => ['updated' => 1]],
                        ['session' => $session] + $options,
                    );
                }, 'w',
            ],

            /* Disabled, as it's illegal to use change streams in transactions
            'watch' => [
                function($collection, $session, $options = []) {
                    $collection->watch(
                        [],
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */
        ];
    }

    public static function collectionReadMethodClosures(): array
    {
        return array_filter(
            self::collectionMethodClosures(),
            fn ($rw) => str_contains($rw[1], 'r'),
        );
    }

    public static function collectionWriteMethodClosures(): array
    {
        return array_filter(
            self::collectionMethodClosures(),
            fn ($rw) => str_contains($rw[1], 'w'),
        );
    }

    #[DataProvider('collectionMethodClosures')]
    public function testMethodDoesNotInheritReadWriteConcernInTransaction(Closure $method): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $session = $this->manager->startSession();
        $session->startTransaction();

        $collection = $this->collection->withOptions([
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'writeConcern' => new WriteConcern(1),
        ]);

        (new CommandObserver())->observe(
            function () use ($method, $collection, $session): void {
                call_user_func($method, $collection, $session);
            },
            function (array $event): void {
                $this->assertObjectNotHasProperty('writeConcern', $event['started']->getCommand());
                $this->assertObjectNotHasProperty('readConcern', $event['started']->getCommand());
            },
        );
    }

    #[DataProvider('collectionWriteMethodClosures')]
    public function testMethodInTransactionWithWriteConcernOption($method): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $session = $this->manager->startSession();
        $session->startTransaction();

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Cannot set write concern after starting a transaction');

        try {
            call_user_func($method, $this->collection, $session, ['writeConcern' => new WriteConcern(1)]);
        } finally {
            $session->endSession();
        }
    }

    #[DataProvider('collectionReadMethodClosures')]
    public function testMethodInTransactionWithReadConcernOption($method): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $session = $this->manager->startSession();
        $session->startTransaction();

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Cannot set read concern after starting a transaction');

        try {
            call_user_func($method, $this->collection, $session, ['readConcern' => new ReadConcern(ReadConcern::LOCAL)]);
        } finally {
            $session->endSession();
        }
    }

    public function testListSearchIndexesInheritTypeMap(): void
    {
        $this->skipIfAtlasSearchIndexIsNotSupported();

        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), ['typeMap' => ['root' => 'array']]);

        // Insert a document to create the collection
        $collection->insertOne(['_id' => 1]);

        try {
            $collection->createSearchIndex(['mappings' => ['dynamic' => false]], ['name' => 'test-search-index']);
        } catch (CommandException $e) {
            // Ignore duplicate errors in case this test is re-run too quickly
            // Index is asynchronously dropped during tearDown, we only need to
            // ensure it exists for this test.
            if ($e->getCode() !== 68 /* IndexAlreadyExists */) {
                throw $e;
            }
        }

        $indexes = $collection->listSearchIndexes();
        $indexes = iterator_to_array($indexes);
        $this->assertCount(1, $indexes);
        $this->assertIsArray($indexes[0]);
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n, array $executeBulkWriteOptions = []): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (int) ($i . $i),
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite, $executeBulkWriteOptions);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
