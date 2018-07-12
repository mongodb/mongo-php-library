<?php

namespace MongoDB\Tests\Collection;

use MongoDB\BSON\Javascript;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Count;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\MapReduce;
use MongoDB\Tests\CommandObserver;
use Exception;
use stdClass;

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
        $options = ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
        $operation->execute($this->getPrimaryServer());

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

        (new CommandObserver)->observe(
            function() {
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
            function(array $event) {
                $command = $event['started']->getCommand();
                $this->assertObjectHasAttribute('lsid', $command);
                $this->assertObjectHasAttribute('maxTimeMS', $command);
                $this->assertObjectHasAttribute('writeConcern', $command);
                $this->assertObjectHasAttribute('sparse', $command->indexes[0]);
                $this->assertObjectHasAttribute('unique', $command->indexes[0]);
            }
        );
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
        $options = ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
        $operation->execute($this->getPrimaryServer());

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
        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInternalType('array', $debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
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

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInternalType('array', $debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testMapReduce()
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(1, this.x); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $result = $this->collection->mapReduce($map, $reduce, $out);

        $this->assertInstanceOf('MongoDB\MapReduceResult', $result);
        $expected = [
            [ '_id' => 1.0, 'value' => 66.0 ],
        ];

        $this->assertSameDocuments($expected, $result);

        $this->assertGreaterThanOrEqual(0, $result->getExecutionTimeMS());
        $this->assertNotEmpty($result->getCounts());
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
