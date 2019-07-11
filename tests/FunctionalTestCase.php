<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Query;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\DropCollection;
use InvalidArgumentException;
use stdClass;
use UnexpectedValueException;

abstract class FunctionalTestCase extends TestCase
{
    protected $manager;

    private $configuredFailPoints = [];

    public function setUp()
    {
        parent::setUp();

        $this->manager = new Manager(static::getUri());
        $this->configuredFailPoints = [];
    }

    public function tearDown()
    {
        $this->disableFailPoints();

        parent::tearDown();
    }

    protected function assertCollectionCount($namespace, $count)
    {
        list($databaseName, $collectionName) = explode('.', $namespace, 2);

        $cursor = $this->manager->executeCommand($databaseName, new Command(['count' => $collectionName]));
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        $this->assertArrayHasKey('n', $document);
        $this->assertEquals($count, $document['n']);
    }

    protected function assertCommandSucceeded($document)
    {
        $document = is_object($document) ? (array) $document : $document;

        $this->assertArrayHasKey('ok', $document);
        $this->assertEquals(1, $document['ok']);
    }

    protected function assertSameObjectId($expectedObjectId, $actualObjectId)
    {
        $this->assertInstanceOf('MongoDB\BSON\ObjectId', $expectedObjectId);
        $this->assertInstanceOf('MongoDB\BSON\ObjectId', $actualObjectId);
        $this->assertEquals((string) $expectedObjectId, (string) $actualObjectId);
    }

    /**
     * Configure a fail point for the test.
     *
     * The fail point will automatically be disabled during tearDown() to avoid
     * affecting a subsequent test.
     *
     * @param array|stdClass $command configureFailPoint command document
     * @throws InvalidArgumentException if $command is not a configureFailPoint command
     */
    protected function configureFailPoint($command)
    {
        if (is_array($command)) {
            $command = (object) $command;
        }

        if ( ! $command instanceof stdClass) {
            throw new InvalidArgumentException('$command is not an array or stdClass instance');
        }

        if (key($command) !== 'configureFailPoint') {
            throw new InvalidArgumentException('$command is not a configureFailPoint command');
        }

        $operation = new DatabaseCommand('admin', $command);
        $cursor = $operation->execute($this->getPrimaryServer());
        $result = $cursor->toArray()[0];

        $this->assertCommandSucceeded($result);

        // Record the fail point so it can be disabled during tearDown()
        $this->configuredFailPoints[] = $command->configureFailPoint;
    }

    /**
     * Creates the test collection with the specified options.
     *
     * If the "writeConcern" option is not specified but is supported by the
     * server, a majority write concern will be used. This is helpful for tests
     * using transactions or secondary reads.
     *
     * @param array $options
     */
    protected function createCollection(array $options = [])
    {
        if (version_compare($this->getServerVersion(), '3.4.0', '>=')) {
            $options += ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
        }

        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
        $operation->execute($this->getPrimaryServer());
    }

    /**
     * Drops the test collection with the specified options.
     *
     * If the "writeConcern" option is not specified but is supported by the
     * server, a majority write concern will be used. This is helpful for tests
     * using transactions or secondary reads.
     *
     * @param array $options
     */
    protected function dropCollection(array $options = [])
    {
        if (version_compare($this->getServerVersion(), '3.4.0', '>=')) {
            $options += ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
        }

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
        $operation->execute($this->getPrimaryServer());
    }

    protected function getFeatureCompatibilityVersion(ReadPreference $readPreference = null)
    {
        if ($this->isShardedCluster()) {
            return $this->getServerVersion($readPreference);
        }

        if (version_compare($this->getServerVersion(), '3.4.0', '<')) {
            return $this->getServerVersion($readPreference);
        }

        $cursor = $this->manager->executeCommand(
            'admin',
            new Command(['getParameter' => 1, 'featureCompatibilityVersion' => 1]),
            $readPreference ?: new ReadPreference(ReadPreference::RP_PRIMARY)
        );

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        // MongoDB 3.6: featureCompatibilityVersion is an embedded document
        if (isset($document['featureCompatibilityVersion']['version']) && is_string($document['featureCompatibilityVersion']['version'])) {
            return $document['featureCompatibilityVersion']['version'];
        }

        // MongoDB 3.4: featureCompatibilityVersion is a string
        if (isset($document['featureCompatibilityVersion']) && is_string($document['featureCompatibilityVersion'])) {
            return $document['featureCompatibilityVersion'];
        }

        throw new UnexpectedValueException('Could not determine featureCompatibilityVersion');
    }

    protected function getPrimaryServer()
    {
        return $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));
    }

    protected function getServerVersion(ReadPreference $readPreference = null)
    {
        $cursor = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(['buildInfo' => 1]),
            $readPreference ?: new ReadPreference(ReadPreference::RP_PRIMARY)
        );

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        if (isset($document['version']) && is_string($document['version'])) {
            return $document['version'];
        }

        throw new UnexpectedValueException('Could not determine server version');
    }

    protected function getServerStorageEngine(ReadPreference $readPreference = null)
    {
        $cursor = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(['serverStatus' => 1]),
            $readPreference ?: new ReadPreference('primary')
        );

        $result = current($cursor->toArray());

        if (isset($result->storageEngine->name) && is_string($result->storageEngine->name)) {
            return $result->storageEngine->name;
        }

        throw new UnexpectedValueException('Could not determine server storage engine');
    }

    protected function isShardedCluster()
    {
        if ($this->getPrimaryServer()->getType() == Server::TYPE_MONGOS) {
            return true;
        }

        return false;
    }

    protected function isShardedClusterUsingReplicasets()
    {
        $cursor = $this->getPrimaryServer()->executeQuery(
            'config.shards',
            new Query([], ['limit' => 1])
        );

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        if (! $document ) {
            return false;
        }

        /**
         * Use regular expression to distinguish between standalone or replicaset:
         * Without a replicaset: "host" : "localhost:4100"
         * With a replicaset: "host" : "dec6d8a7-9bc1-4c0e-960c-615f860b956f/localhost:4400,localhost:4401"
         */
        return preg_match('@^.*/.*:\d+@', $document['host']);
    }

    protected function skipIfChangeStreamIsNotSupported()
    {
        switch ( $this->getPrimaryServer()->getType() )
        {
            case Server::TYPE_MONGOS:
                if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
                    $this->markTestSkipped('$changeStream is only supported on MongoDB 3.6 or higher');
                }
                if (!$this->isShardedClusterUsingReplicasets()) {
                    $this->markTestSkipped('$changeStream is only supported with replicasets');
                }
                break;

            case Server::TYPE_RS_PRIMARY:
                if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
                    $this->markTestSkipped('$changeStream is only supported on FCV 3.6 or higher');
                }
                break;

            default:
                $this->markTestSkipped('$changeStream is not supported');
        }
    }

    protected function skipIfCausalConsistencyIsNotSupported()
    {
        switch ( $this->getPrimaryServer()->getType() )
        {
            case Server::TYPE_MONGOS:
                if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
                    $this->markTestSkipped('Causal Consistency is only supported on MongoDB 3.6 or higher');
                }
                if (!$this->isShardedClusterUsingReplicasets()) {
                    $this->markTestSkipped('Causal Consistency is only supported with replicasets');
                }
                break;

            case Server::TYPE_RS_PRIMARY:
                if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
                    $this->markTestSkipped('Causal Consistency is only supported on FCV 3.6 or higher');
                }
                if ($this->getServerStorageEngine() !== 'wiredTiger') {
                    $this->markTestSkipped('Causal Consistency requires WiredTiger storage engine');
                }
                break;

            default:
                $this->markTestSkipped('Causal Consistency is not supported');
        }
    }

    protected function skipIfTransactionsAreNotSupported()
    {
        if ($this->getPrimaryServer()->getType() === Server::TYPE_STANDALONE) {
            $this->markTestSkipped('Transactions are not supported on standalone servers');
        }

        // TODO: MongoDB 4.2 should support sharded clusters (see: PHPLIB-374)
        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Transactions are not supported on sharded clusters');
        }

        if (version_compare($this->getFeatureCompatibilityVersion(), '4.0', '<')) {
            $this->markTestSkipped('Transactions are only supported on FCV 4.0 or higher');
        }

        if ($this->getServerStorageEngine() !== 'wiredTiger') {
            $this->markTestSkipped('Transactions require WiredTiger storage engine');
        }
    }

    /**
     * Disables any fail points that were configured earlier in the test.
     *
     * This tracks fail points set via configureFailPoint() and should be called
     * during tearDown().
     */
    private function disableFailPoints()
    {
        if (empty($this->configuredFailPoints)) {
            return;
        }

        $server = $this->getPrimaryServer();

        foreach ($this->configuredFailPoints as $failPoint) {
            $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
            $operation->execute($server);
        }
    }
}
