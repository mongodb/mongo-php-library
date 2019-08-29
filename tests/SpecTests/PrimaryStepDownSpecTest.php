<?php

namespace MongoDB\Tests\SpecTests;

use IteratorIterator;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\Exception as DriverException;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\BulkWrite;
use MongoDB\Tests\CommandObserver;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use UnexpectedValueException;
use function current;

/**
 * @see https://github.com/mongodb/specifications/tree/master/source/connections-survive-step-down/tests
 */
class PrimaryStepDownSpecTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    const INTERRUPTED_AT_SHUTDOWN = 11600;
    const NOT_MASTER = 10107;
    const SHUTDOWN_IN_PROGRESS = 91;

    /** @var Client */
    private $client;

    /** @var Collection */
    private $collection;

    private function doSetUp()
    {
        parent::setUp();

        $this->client = new Client(static::getUri(), ['retryWrites' => false, 'heartbeatFrequencyMS' => 500, 'serverSelectionTimeoutMS' => 20000, 'serverSelectionTryOnce' => false]);

        $this->dropAndRecreateCollection();
        $this->collection = $this->client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * @see https://github.com/mongodb/specifications/tree/master/source/connections-survive-step-down/tests#id10
     */
    public function testNotMasterKeepsConnectionPool()
    {
        $runOn = [(object) ['minServerVersion' => '4.1.11', 'topology' => [self::TOPOLOGY_REPLICASET]]];
        $this->checkServerRequirements($runOn);

        // Set a fail point
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => self::NOT_MASTER,
            ],
        ]);

        $totalConnectionsCreated = $this->getTotalConnectionsCreated();

        // Execute an insert into the test collection of a {test: 1} document.
        try {
            $this->insertDocuments(1);
        } catch (BulkWriteException $e) {
            // Verify that the insert failed with an operation failure with 10107 code.
            $this->assertSame(self::NOT_MASTER, $e->getCode());
        }

        // Execute an insert into the test collection of a {test: 1} document and verify that it succeeds.
        $result = $this->insertDocuments(1);
        $this->assertSame(1, $result->getInsertedCount());

        // Verify that the connection pool has not been cleared
        $this->assertSame($totalConnectionsCreated, $this->getTotalConnectionsCreated());
    }

    /**
     * @see https://github.com/mongodb/specifications/tree/master/source/connections-survive-step-down/tests#id11
     */
    public function testNotMasterResetConnectionPool()
    {
        $runOn = [(object) ['minServerVersion' => '4.0.0', 'maxServerVersion' => '4.0.999', 'topology' => [self::TOPOLOGY_REPLICASET]]];
        $this->checkServerRequirements($runOn);

        // Set a fail point
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => self::NOT_MASTER,
            ],
        ]);

        $totalConnectionsCreated = $this->getTotalConnectionsCreated();

        // Execute an insert into the test collection of a {test: 1} document.
        try {
            $this->insertDocuments(1);
        } catch (BulkWriteException $e) {
            // Verify that the insert failed with an operation failure with 10107 code.
            $this->assertSame(self::NOT_MASTER, $e->getCode());
        }

        // Verify that the connection pool has been cleared
        $this->assertSame($totalConnectionsCreated + 1, $this->getTotalConnectionsCreated());
    }

    /**
     * @see https://github.com/mongodb/specifications/tree/master/source/connections-survive-step-down/tests#id12
     */
    public function testShutdownResetConnectionPool()
    {
        $runOn = [(object) ['minServerVersion' => '4.0.0']];
        $this->checkServerRequirements($runOn);

        // Set a fail point
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => self::SHUTDOWN_IN_PROGRESS,
            ],
        ]);

        $totalConnectionsCreated = $this->getTotalConnectionsCreated();

        // Execute an insert into the test collection of a {test: 1} document.
        try {
            $this->insertDocuments(1);
        } catch (BulkWriteException $e) {
            // Verify that the insert failed with an operation failure with 91 code.
            $this->assertSame(self::SHUTDOWN_IN_PROGRESS, $e->getCode());
        }

        // Verify that the connection pool has been cleared
        $this->assertSame($totalConnectionsCreated + 1, $this->getTotalConnectionsCreated());
    }

    /**
     * @see https://github.com/mongodb/specifications/tree/master/source/connections-survive-step-down/tests#id13
     */
    public function testInterruptedAtShutdownResetConnectionPool()
    {
        $runOn = [(object) ['minServerVersion' => '4.0.0']];
        $this->checkServerRequirements($runOn);

        // Set a fail point
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => self::INTERRUPTED_AT_SHUTDOWN,
            ],
        ]);

        $totalConnectionsCreated = $this->getTotalConnectionsCreated();

        // Execute an insert into the test collection of a {test: 1} document.
        try {
            $this->insertDocuments(1);
        } catch (BulkWriteException $e) {
            // Verify that the insert failed with an operation failure with 11600 code.
            $this->assertSame(self::INTERRUPTED_AT_SHUTDOWN, $e->getCode());
        }

        // Verify that the connection pool has been cleared
        $this->assertSame($totalConnectionsCreated + 1, $this->getTotalConnectionsCreated());
    }

    /**
     * @see https://github.com/mongodb/specifications/tree/master/source/connections-survive-step-down/tests#id9
     */
    public function testGetMoreIteration()
    {
        $this->markTestSkipped('Test causes subsequent failures in other tests (see PHPLIB-471)');

        $runOn = [(object) ['minServerVersion' => '4.1.11', 'topology' => [self::TOPOLOGY_REPLICASET]]];
        $this->checkServerRequirements($runOn);

        // Insert 5 documents into a collection with a majority write concern.
        $this->insertDocuments(5);

        // Start a find operation on the collection with a batch size of 2, and retrieve the first batch of results.
        $cursor = $this->collection->find([], ['batchSize' => 2]);

        $iterator = new IteratorIterator($cursor);
        $iterator->rewind();
        $this->assertTrue($iterator->valid());

        $iterator->next();
        $this->assertTrue($iterator->valid());

        $totalConnectionsCreated = $this->getTotalConnectionsCreated();

        // Send a {replSetStepDown: 5, force: true} command to the current primary and verify that the command succeeded
        $primary = $this->client->getManager()->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));
        $primary->executeCommand('admin', new Command(['replSetStepDown' => 5, 'force' => true]));

        // Retrieve the next batch of results from the cursor obtained in the find operation, and verify that this operation succeeded.
        $events = [];
        $observer = new CommandObserver();
        $observer->observe(
            function () use ($iterator) {
                $iterator->next();
            },
            function ($event) use (&$events) {
                $events[] = $event;
            }
        );
        $this->assertTrue($iterator->valid());
        $this->assertCount(1, $events);
        $this->assertSame('getMore', $events[0]['started']->getCommandName());

        // Verify that no new connections have been created
        $this->assertSame($totalConnectionsCreated, $this->getTotalConnectionsCreated($cursor->getServer()));

        // Wait to allow primary election to complete and prevent subsequent test failures
        $this->waitForMasterReelection();
    }

    private function insertDocuments($count)
    {
        $operations = [];

        for ($i = 1; $i <= $count; $i++) {
            $operations[] = [
                BulkWrite::INSERT_ONE => [['test' => $i]],
            ];
        }

        return $this->collection->bulkWrite($operations, ['writeConcern' => new WriteConcern('majority')]);
    }

    private function dropAndRecreateCollection()
    {
        $this->client->selectCollection($this->getDatabaseName(), $this->getCollectionName())->drop();
        $this->client->selectDatabase($this->getDatabaseName())->command(['create' => $this->getCollectionName()]);
    }

    private function getTotalConnectionsCreated(Server $server = null)
    {
        $server = $server ?: $this->client->getManager()->selectServer(new ReadPreference('primary'));

        $cursor = $server->executeCommand(
            $this->getDatabaseName(),
            new Command(['serverStatus' => 1]),
            new ReadPreference(ReadPreference::RP_PRIMARY)
        );

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        if (isset($document['connections'], $document['connections']['totalCreated'])) {
            return (int) $document['connections']['totalCreated'];
        }

        throw new UnexpectedValueException('Could not determine number of total connections');
    }

    private function waitForMasterReelection()
    {
        try {
            $this->insertDocuments(1);

            return;
        } catch (DriverException $e) {
            $this->client->getManager()->selectServer(new ReadPreference('primary'));

            return;
        }

        $this->fail('Expected primary to be re-elected within 20 seconds.');
    }
}
