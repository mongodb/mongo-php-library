<?php

namespace MongoDB\Tests;

use InvalidArgumentException;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\DropCollection;
use stdClass;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use UnexpectedValueException;
use function array_merge;
use function count;
use function current;
use function explode;
use function implode;
use function is_array;
use function is_object;
use function is_string;
use function key;
use function ob_get_clean;
use function ob_start;
use function parse_url;
use function phpinfo;
use function preg_match;
use function preg_quote;
use function sprintf;
use function version_compare;
use const INFO_MODULES;

abstract class FunctionalTestCase extends TestCase
{
    use SetUpTearDownTrait;

    /** @var Manager */
    protected $manager;

    /** @var array */
    private $configuredFailPoints = [];

    private function doSetUp()
    {
        parent::setUp();

        $this->manager = new Manager(static::getUri());
        $this->configuredFailPoints = [];
    }

    private function doTearDown()
    {
        $this->disableFailPoints();

        parent::tearDown();
    }

    public static function getUri($allowMultipleMongoses = false)
    {
        $uri = parent::getUri();

        if ($allowMultipleMongoses) {
            return $uri;
        }

        $urlParts = parse_url($uri);
        if ($urlParts === false) {
            return $uri;
        }

        // Only modify URIs using the mongodb scheme
        if ($urlParts['scheme'] !== 'mongodb') {
            return $uri;
        }

        $hosts = explode(',', $urlParts['host']);
        $numHosts = count($hosts);
        if ($numHosts === 1) {
            return $uri;
        }

        $manager = new Manager($uri);
        if ($manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY))->getType() !== Server::TYPE_MONGOS) {
            return $uri;
        }

        // Re-append port to last host
        if (isset($urlParts['port'])) {
            $hosts[$numHosts-1] .= ':' . $urlParts['port'];
        }

        $parts = ['mongodb://'];

        if (isset($urlParts['user'], $urlParts['pass'])) {
            $parts += [
                $urlParts['user'],
                ':',
                $urlParts['pass'],
                '@',
            ];
        }

        $parts[] = $hosts[0];

        if (isset($urlParts['path'])) {
            $parts[] = $urlParts['path'];
        }
        if (isset($urlParts['query'])) {
            $parts = array_merge($parts, [
                '?',
                $urlParts['query'],
            ]);
        }

        return implode('', $parts);
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
        $this->assertInstanceOf(ObjectId::class, $expectedObjectId);
        $this->assertInstanceOf(ObjectId::class, $actualObjectId);
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
    public function configureFailPoint($command, Server $server = null)
    {
        if (! $this->isFailCommandSupported()) {
            $this->markTestSkipped('failCommand is only supported on mongod >= 4.0.0 and mongos >= 4.1.5.');
        }

        if (! $this->isFailCommandEnabled()) {
            $this->markTestSkipped('The enableTestCommands parameter is not enabled.');
        }

        if (is_array($command)) {
            $command = (object) $command;
        }

        if (! $command instanceof stdClass) {
            throw new InvalidArgumentException('$command is not an array or stdClass instance');
        }

        if (key($command) !== 'configureFailPoint') {
            throw new InvalidArgumentException('$command is not a configureFailPoint command');
        }

        $failPointServer = $server ?: $this->getPrimaryServer();

        $operation = new DatabaseCommand('admin', $command);
        $cursor = $operation->execute($failPointServer);
        $result = $cursor->toArray()[0];

        $this->assertCommandSucceeded($result);

        // Record the fail point so it can be disabled during tearDown()
        $this->configuredFailPoints[] = [$command->configureFailPoint, $failPointServer];
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

    protected function isReplicaSet()
    {
        return $this->getPrimaryServer()->getType() == Server::TYPE_RS_PRIMARY;
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

        if (! $document) {
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
        switch ($this->getPrimaryServer()->getType()) {
            case Server::TYPE_MONGOS:
                if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
                    $this->markTestSkipped('$changeStream is only supported on MongoDB 3.6 or higher');
                }
                if (! $this->isShardedClusterUsingReplicasets()) {
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
        switch ($this->getPrimaryServer()->getType()) {
            case Server::TYPE_MONGOS:
                if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
                    $this->markTestSkipped('Causal Consistency is only supported on MongoDB 3.6 or higher');
                }
                if (! $this->isShardedClusterUsingReplicasets()) {
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

    protected function skipIfClientSideEncryptionIsNotSupported()
    {
        if (version_compare($this->getFeatureCompatibilityVersion(), '4.2', '<')) {
            $this->markTestSkipped('Client Side Encryption only supported on FCV 4.2 or higher');
        }

        if ($this->getModuleInfo('libmongocrypt') === 'disabled') {
            $this->markTestSkipped('Client Side Encryption is not enabled in the MongoDB extension');
        }
    }

    protected function skipIfTransactionsAreNotSupported()
    {
        if ($this->getPrimaryServer()->getType() === Server::TYPE_STANDALONE) {
            $this->markTestSkipped('Transactions are not supported on standalone servers');
        }

        if ($this->isShardedCluster()) {
            if (! $this->isShardedClusterUsingReplicasets()) {
                $this->markTestSkipped('Transactions are not supported on sharded clusters without replica sets');
            }

            if (version_compare($this->getFeatureCompatibilityVersion(), '4.2', '<')) {
                $this->markTestSkipped('Transactions are only supported on FCV 4.2 or higher');
            }

            return;
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

        foreach ($this->configuredFailPoints as list($failPoint, $server)) {
            $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
            $operation->execute($server);
        }
    }

    /**
     * @param string $row
     *
     * @return string|null
     */
    private function getModuleInfo($row)
    {
        ob_start();
        phpinfo(INFO_MODULES);
        $info = ob_get_clean();

        $pattern = sprintf('/^%s([\w ]+)$/m', preg_quote($row . ' => '));

        if (preg_match($pattern, $info, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Checks if the failCommand command is supported on this server version
     *
     * @return bool
     */
    private function isFailCommandSupported()
    {
        $minVersion = $this->isShardedCluster() ? '4.1.5' : '4.0.0';

        return version_compare($this->getServerVersion(), $minVersion, '>=');
    }

    /**
     * Checks if the failCommand command is enabled by checking the enableTestCommands parameter
     *
     * @return bool
     */
    private function isFailCommandEnabled()
    {
        try {
            $cursor = $this->manager->executeCommand(
                'admin',
                new Command(['getParameter' => 1, 'enableTestCommands' => 1])
            );

            $document = current($cursor->toArray());
        } catch (CommandException $e) {
            return false;
        }

        return isset($document->enableTestCommands) && $document->enableTestCommands === true;
    }
}
