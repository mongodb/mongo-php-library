<?php

namespace MongoDB\Tests\Operation;

use IteratorIterator;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadPreference;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\Find;
use MongoDB\Tests\CommandObserver;
use function microtime;
use function version_compare;

class FindFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        (new CommandObserver())->observe(
            function () {
                $operation = new Find(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testHintOption()
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['_id' => 1, 'x' => 1]);
        $bulkWrite->insert(['_id' => 2, 'x' => 2]);
        $bulkWrite->insert(['_id' => 3, 'y' => 3]);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $createIndexes = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [
            ['key' => ['x' => 1], 'sparse' => true, 'name' => 'sparse_x'],
            ['key' => ['y' => 1]],
        ]);
        $createIndexes->execute($this->getPrimaryServer());

        $hintsUsingSparseIndex = [
            ['x' => 1],
            'sparse_x',
        ];

        foreach ($hintsUsingSparseIndex as $hint) {
            $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['hint' => $hint]);
            $cursor = $operation->execute($this->getPrimaryServer());

            $expectedDocuments = [
                (object) ['_id' => 1, 'x' => 1],
                (object) ['_id' => 2, 'x' => 2],
            ];

            $this->assertEquals($expectedDocuments, $cursor->toArray());
        }

        $hintsNotUsingSparseIndex = [
            ['_id' => 1],
            ['y' => 1],
            'y_1',
        ];

        foreach ($hintsNotUsingSparseIndex as $hint) {
            $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['hint' => $hint]);
            $cursor = $operation->execute($this->getPrimaryServer());

            $expectedDocuments = [
                (object) ['_id' => 1, 'x' => 1],
                (object) ['_id' => 2, 'x' => 2],
                (object) ['_id' => 3, 'y' => 3],
            ];

            $this->assertEquals($expectedDocuments, $cursor->toArray());
        }
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new Find(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOption(array $typeMap, array $expectedDocuments)
    {
        $this->createFixtures(3);

        $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['typeMap' => $typeMap]);
        $cursor = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($expectedDocuments, $cursor->toArray());
    }

    public function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['_id' => 1, 'x' => ['foo' => 'bar']],
                    ['_id' => 2, 'x' => ['foo' => 'bar']],
                    ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                [
                    (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
                    (object) ['_id' => 2, 'x' => ['foo' => 'bar']],
                    (object) ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                    ['_id' => 2, 'x' => (object) ['foo' => 'bar']],
                    ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
                ],
            ],
        ];
    }

    public function testMaxAwaitTimeMS()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('maxAwaitTimeMS option is not supported');
        }

        $maxAwaitTimeMS = 100;

        /* Calculate an approximate pivot to use for time assertions. We will
         * assert that the duration of blocking responses is greater than this
         * value, and vice versa. */
        $pivot = $maxAwaitTimeMS * 0.001 * 0.9;

        // Create a capped collection.
        $databaseName = $this->getDatabaseName();
        $cappedCollectionName = $this->getCollectionName();
        $cappedCollectionOptions = [
            'capped' => true,
            'max' => 100,
            'size' => 1048576,
        ];

        $operation = new CreateCollection($databaseName, $cappedCollectionName, $cappedCollectionOptions);
        $operation->execute($this->getPrimaryServer());

        // Insert documents into the capped collection.
        $bulkWrite = new BulkWrite(['ordered' => true]);
        $bulkWrite->insert(['_id' => 1]);
        $bulkWrite->insert(['_id' => 2]);
        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $operation = new Find($databaseName, $cappedCollectionName, [], ['cursorType' => Find::TAILABLE_AWAIT, 'maxAwaitTimeMS' => $maxAwaitTimeMS]);
        $cursor = $operation->execute($this->getPrimaryServer());
        $it = new IteratorIterator($cursor);

        /* The initial query includes the one and only document in its result
         * batch, so we should not expect a delay. */
        $startTime = microtime(true);
        $it->rewind();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($pivot, $duration);

        $this->assertTrue($it->valid());
        $this->assertSameDocument(['_id' => 1], $it->current());

        /* Advancing again takes us to the last document of the result batch,
         * but still should not issue a getMore */
        $startTime = microtime(true);
        $it->next();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($pivot, $duration);

        $this->assertTrue($it->valid());
        $this->assertSameDocument(['_id' => 2], $it->current());

        /* Now that we've reached the end of the initial result batch, advancing
         * again will issue a getMore. Expect to wait at least maxAwaitTimeMS,
         * since no new documents should be inserted to wake up the server's
         * query thread. Also ensure we don't wait too long (server default is
         * one second). */
        $startTime = microtime(true);
        $it->next();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThan($pivot, $duration);
        $this->assertLessThan(0.5, $duration);

        $this->assertFalse($it->valid());
    }

    public function testReadPreferenceWithinTransaction()
    {
        $this->skipIfTransactionsAreNotSupported();

        // Collection must be created before the transaction starts
        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        try {
            $this->createFixtures(3, ['session' => $session]);

            $filter = ['_id' => ['$lt' => 3]];
            $options = [
                'readPreference' => new ReadPreference('primary'),
                'session' => $session,
            ];

            $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), $filter, $options);
            $cursor = $operation->execute($this->getPrimaryServer());

            $expected = [
                ['_id' => 1, 'x' => ['foo' => 'bar']],
                ['_id' => 2, 'x' => ['foo' => 'bar']],
            ];

            $this->assertSameDocuments($expected, $cursor);

            $session->commitTransaction();
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
                'x' => (object) ['foo' => 'bar'],
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite, $executeBulkWriteOptions);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
