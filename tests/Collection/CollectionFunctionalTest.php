<?php

namespace MongoDB\Tests\Collection;

use Closure;
use MongoDB\BSON\Javascript;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\MapReduceResult;
use MongoDB\Operation\Count;
use MongoDB\Tests\CommandObserver;
use function array_filter;
use function call_user_func;
use function is_scalar;
use function json_encode;
use function strchr;
use function usort;
use function version_compare;

/**
 * Functional tests for the Collection class.
 */
class CollectionFunctionalTest extends FunctionalTestCase
{
    /**
     * @dataProvider provideInvalidDatabaseAndCollectionNames
     */
    public function testConstructorDatabaseNameArgument($databaseName)
    {
        $this->expectException(InvalidArgumentException::class);
        // TODO: Move to unit test once ManagerInterface can be mocked (PHPC-378)
        new Collection($this->manager, $databaseName, $this->getCollectionName());
    }

    /**
     * @dataProvider provideInvalidDatabaseAndCollectionNames
     */
    public function testConstructorCollectionNameArgument($collectionName)
    {
        $this->expectException(InvalidArgumentException::class);
        // TODO: Move to unit test once ManagerInterface can be mocked (PHPC-378)
        new Collection($this->manager, $this->getDatabaseName(), $collectionName);
    }

    public function provideInvalidDatabaseAndCollectionNames()
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidReadConcernValues() as $value) {
            $options[][] = ['readConcern' => $value];
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    public function testGetManager()
    {
        $this->assertSame($this->manager, $this->collection->getManager());
    }

    public function testToString()
    {
        $this->assertEquals($this->getNamespace(), (string) $this->collection);
    }

    public function getGetCollectionName()
    {
        $this->assertEquals($this->getCollectionName(), $this->collection->getCollectionName());
    }

    public function getGetDatabaseName()
    {
        $this->assertEquals($this->getDatabaseName(), $this->collection->getDatabaseName());
    }

    public function testGetNamespace()
    {
        $this->assertEquals($this->getNamespace(), $this->collection->getNamespace());
    }

    public function testAggregateWithinTransaction()
    {
        $this->skipIfTransactionsAreNotSupported();

        // Collection must be created before the transaction starts
        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        try {
            $this->createFixtures(3, ['session' => $session]);

            $cursor = $this->collection->aggregate(
                [['$match' => ['_id' => ['$lt' => 3]]]],
                ['session' => $session]
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

    public function testCreateIndexSplitsCommandOptions()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $this->collection->createIndex(
                    ['x' => 1],
                    [
                        'maxTimeMS' => 1000,
                        'session' => $this->manager->startSession(),
                        'sparse' => true,
                        'unique' => true,
                        'writeConcern' => new WriteConcern(1),
                    ]
                );
            },
            function (array $event) {
                $command = $event['started']->getCommand();
                $this->assertObjectHasAttribute('lsid', $command);
                $this->assertObjectHasAttribute('maxTimeMS', $command);
                $this->assertObjectHasAttribute('writeConcern', $command);
                $this->assertObjectHasAttribute('sparse', $command->indexes[0]);
                $this->assertObjectHasAttribute('unique', $command->indexes[0]);
            }
        );
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testDistinctWithTypeMap(array $typeMap, array $expectedDocuments)
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

                $a = json_encode($a);
                $b = json_encode($b);
            }

            return $a < $b ? -1 : 1;
        };

        usort($expectedDocuments, $sort);
        usort($values, $sort);

        $this->assertEquals($expectedDocuments, $values);
    }

    public function provideTypeMapOptionsAndExpectedDocuments()
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

    public function testDrop()
    {
        $writeResult = $this->collection->insertOne(['x' => 1]);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->collection->drop();
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    /**
     * @todo Move this to a unit test once Manager can be mocked
     */
    public function testDropIndexShouldNotAllowWildcardCharacter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->collection->dropIndex('*');
    }

    public function testExplain()
    {
        $this->createFixtures(3);

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);

        $result = $this->collection->explain($operation);

        $this->assertArrayHasKey('queryPlanner', $result);
    }

    public function testFindOne()
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

    public function testFindWithinTransaction()
    {
        $this->skipIfTransactionsAreNotSupported();

        // Collection must be created before the transaction starts
        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        try {
            $this->createFixtures(3, ['session' => $session]);

            $cursor = $this->collection->find(
                ['_id' => ['$lt' => 3]],
                ['session' => $session]
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

    public function testWithOptionsInheritsOptions()
    {
        $collectionOptions = [
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);
        $clone = $collection->withOptions();
        $debug = $clone->__debugInfo();

        $this->assertSame($this->manager, $debug['manager']);
        $this->assertSame($this->getDatabaseName(), $debug['databaseName']);
        $this->assertSame($this->getCollectionName(), $debug['collectionName']);
        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testWithOptionsPassesOptions()
    {
        $collectionOptions = [
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $clone = $this->collection->withOptions($collectionOptions);
        $debug = $clone->__debugInfo();

        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testMapReduce()
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(1, this.x); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $result = $this->collection->mapReduce($map, $reduce, $out);

        $this->assertInstanceOf(MapReduceResult::class, $result);
        $expected = [
            [ '_id' => 1.0, 'value' => 66.0 ],
        ];

        $this->assertSameDocuments($expected, $result);

        $this->assertGreaterThanOrEqual(0, $result->getExecutionTimeMS());
        $this->assertNotEmpty($result->getCounts());
    }

    public function collectionMethodClosures()
    {
        return [
            [
                function ($collection, $session, $options = []) {
                    $collection->aggregate(
                        [['$match' => ['_id' => ['$lt' => 3]]]],
                        ['session' => $session] + $options
                    );
                }, 'rw',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->bulkWrite(
                        [['insertOne' => [['test' => 'foo']]]],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            /* Disabled, as count command can't be used in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->count(
                        [],
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */

            [
                function ($collection, $session, $options = []) {
                    $collection->countDocuments(
                        [],
                        ['session' => $session] + $options
                    );
                }, 'r',
            ],

            /* Disabled, as it's illegal to use createIndex command in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->createIndex(
                        ['test' => 1],
                        ['session' => $session] + $options
                    );
                }, 'w'
            ],
            */

            [
                function ($collection, $session, $options = []) {
                    $collection->deleteMany(
                        ['test' => 'foo'],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->deleteOne(
                        ['test' => 'foo'],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->distinct(
                        '_id',
                        [],
                        ['session' => $session] + $options
                    );
                }, 'r',
            ],

            /* Disabled, as it's illegal to use drop command in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->drop(
                        ['session' => $session] + $options
                    );
                }, 'w'
            ],
            */

            /* Disabled, as it's illegal to use dropIndexes command in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->dropIndex(
                        '_id_1',
                        ['session' => $session] + $options
                    );
                }, 'w'
            ], */

            /* Disabled, as it's illegal to use dropIndexes command in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->dropIndexes(
                        ['session' => $session] + $options
                    );
                }, 'w'
            ],
            */

            /* Disabled, as count command can't be used in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->estimatedDocumentCount(
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */

            [
                function ($collection, $session, $options = []) {
                    $collection->find(
                        ['test' => 'foo'],
                        ['session' => $session] + $options
                    );
                }, 'r',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->findOne(
                        ['test' => 'foo'],
                        ['session' => $session] + $options
                    );
                }, 'r',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->findOneAndDelete(
                        ['test' => 'foo'],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->findOneAndReplace(
                        ['test' => 'foo'],
                        [],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->findOneAndUpdate(
                        ['test' => 'foo'],
                        ['$set' => ['updated' => 1]],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->insertMany(
                        [
                            ['test' => 'foo'],
                            ['test' => 'bar'],
                        ],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->insertOne(
                        ['test' => 'foo'],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            /* Disabled, as it's illegal to use listIndexes command in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->listIndexes(
                        ['session' => $session] + $options
                    );
                }, 'r'
            ],
            */

            /* Disabled, as it's illegal to use mapReduce command in transactions
            [
                function($collection, $session, $options = []) {
                    $collection->mapReduce(
                        new \MongoDB\BSON\Javascript('function() { emit(this.state, this.pop); }'),
                        new \MongoDB\BSON\Javascript('function(key, values) { return Array.sum(values) }'),
                        ['inline' => 1],
                        ['session' => $session] + $options
                    );
                }, 'rw'
            ],
            */

            [
                function ($collection, $session, $options = []) {
                    $collection->replaceOne(
                        ['test' => 'foo'],
                        [],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->updateMany(
                        ['test' => 'foo'],
                        ['$set' => ['updated' => 1]],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            [
                function ($collection, $session, $options = []) {
                    $collection->updateOne(
                        ['test' => 'foo'],
                        ['$set' => ['updated' => 1]],
                        ['session' => $session] + $options
                    );
                }, 'w',
            ],

            /* Disabled, as it's illegal to use change streams in transactions
            [
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

    public function collectionReadMethodClosures()
    {
        return array_filter(
            $this->collectionMethodClosures(),
            function ($rw) {
                if (strchr($rw[1], 'r') !== false) {
                    return true;
                }
            }
        );
    }

    public function collectionWriteMethodClosures()
    {
        return array_filter(
            $this->collectionMethodClosures(),
            function ($rw) {
                if (strchr($rw[1], 'w') !== false) {
                    return true;
                }
            }
        );
    }

    /**
     * @dataProvider collectionMethodClosures
     */
    public function testMethodDoesNotInheritReadWriteConcernInTranasaction(Closure $method)
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        $collection = $this->collection->withOptions([
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'writeConcern' => new WriteConcern(1),
        ]);

        (new CommandObserver())->observe(
            function () use ($method, $collection, $session) {
                call_user_func($method, $collection, $session);
            },
            function (array $event) {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    /**
     * @dataProvider collectionWriteMethodClosures
     */
    public function testMethodInTransactionWithWriteConcernOption($method)
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('"writeConcern" option cannot be specified within a transaction');

        try {
            call_user_func($method, $this->collection, $session, ['writeConcern' => new WriteConcern(1)]);
        } finally {
            $session->endSession();
        }
    }

    /**
     * @dataProvider collectionReadMethodClosures
     */
    public function testMethodInTransactionWithReadConcernOption($method)
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('"readConcern" option cannot be specified within a transaction');

        try {
            call_user_func($method, $this->collection, $session, ['readConcern' => new ReadConcern(ReadConcern::LOCAL)]);
        } finally {
            $session->endSession();
        }
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     * @param array   $executeBulkWriteOptions
     */
    private function createFixtures($n, array $executeBulkWriteOptions = [])
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite, $executeBulkWriteOptions);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
