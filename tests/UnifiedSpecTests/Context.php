<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use stdClass;
use function array_key_exists;
use function assertArrayHasKey;
use function assertCount;
use function assertInstanceOf;
use function assertInternalType;
use function assertNotEmpty;
use function assertNotFalse;
use function assertRegExp;
use function assertStringStartsWith;
use function count;
use function current;
use function explode;
use function fopen;
use function fwrite;
use function hex2bin;
use function implode;
use function key;
use function parse_url;
use function rewind;
use function strlen;
use function strpos;
use function substr_replace;
use const PHP_URL_HOST;

/**
 * Execution context for spec tests.
 *
 * This object tracks state that would be difficult to store on the test itself
 * due to the design of PHPUnit's data providers and setUp/tearDown methods.
 */
final class Context
{
    /** @var EntityMap */
    private $entityMap;

    /** @var EventObserver[] */
    private $eventObserversByClient = [];

    /** @var Client */
    private $internalClient;

    /** @var string */
    private $uri;

    public function __construct(Client $internalClient, string $uri)
    {
        $this->entityMap = new EntityMap();
        $this->internalClient = $internalClient;
        $this->uri = $uri;
    }

    /**
     * Create entities for "createEntities".
     *
     * @param array $createEntities
     */
    public function createEntities(array $entities)
    {
        foreach ($entities as $entity) {
            assertInternalType('object', $entity);
            $entity = (array) $entity;
            assertCount(1, $entity);

            $type = key($entity);
            $def = current($entity);
            assertInternalType('object', $def);

            $id = $def->id ?? null;
            assertInternalType('string', $id);

            switch ($type) {
                case 'client':
                    $this->createClient($id, $def);
                    break;

                case 'database':
                    $this->createDatabase($id, $def);
                    break;

                case 'collection':
                    $this->createCollection($id, $def);
                    break;

                case 'session':
                    $this->createSession($id, $def);
                    break;

                case 'bucket':
                    $this->createBucket($id, $def);
                    break;

                case 'stream':
                    $this->createStream($id, $def);
                    break;

                default:
                    throw new LogicException('Unsupported entity type: ' . $type);
            }
        }
    }

    public function getEntityMap() : EntityMap
    {
        return $this->entityMap;
    }

    public function getInternalClient() : Client
    {
        return $this->internalClient;
    }

    public function isActiveClient(string $clientId) : bool
    {
        return $this->activeClient === $clientId;
    }

    public function setActiveClient(string $clientId = null)
    {
        $this->activeClient = $clientId;
    }

    public function assertExpectedEventsForClients(array $expectedEventsForClients)
    {
        assertNotEmpty($expectedEventsForClients);

        foreach ($expectedEventsForClients as $expectedEventsForClient) {
            assertInternalType('object', $expectedEventsForClient);
            Util::assertHasOnlyKeys($expectedEventsForClient, ['client', 'events']);

            $client = $expectedEventsForClient->client ?? null;
            $expectedEvents = $expectedEventsForClient->events ?? null;

            assertInternalType('string', $client);
            assertArrayHasKey($client, $this->eventObserversByClient);
            assertInternalType('array', $expectedEvents);

            $this->eventObserversByClient[$client]->assert($expectedEvents);
        }
    }

    public function startEventObservers()
    {
        foreach ($this->eventObserversByClient as $eventObserver) {
            $eventObserver->start();
        }
    }

    public function stopEventObservers()
    {
        foreach ($this->eventObserversByClient as $eventObserver) {
            $eventObserver->stop();
        }
    }

    public function getEventObserverForClient(string $id) : EventObserver
    {
        assertArrayHasKey($id, $this->eventObserversByClient);

        return $this->eventObserversByClient[$id];
    }

    private function createClient(string $id, stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['id', 'uriOptions', 'useMultipleMongoses', 'observeEvents', 'ignoreCommandMonitoringEvents']);

        $useMultipleMongoses = $o->useMultipleMongoses ?? null;
        $observeEvents = $o->observeEvents ?? null;
        $ignoreCommandMonitoringEvents = $o->ignoreCommandMonitoringEvents ?? [];

        $uri = $this->uri;

        if (isset($useMultipleMongoses)) {
            assertInternalType('bool', $useMultipleMongoses);

            if ($useMultipleMongoses) {
                self::requireMultipleMongoses($uri);
            } else {
                $uri = self::removeMultipleMongoses($uri);
            }
        }

        $uriOptions = [];

        if (isset($o->uriOptions)) {
            assertInternalType('object', $o->uriOptions);
            /* TODO: If readPreferenceTags is set, assert it is an array of
             * strings and convert to an array of documents expected by the
             * PHP driver. */
            $uriOptions = (array) $o->uriOptions;
        }

        if (isset($observeEvents)) {
            assertInternalType('array', $observeEvents);
            assertInternalType('array', $ignoreCommandMonitoringEvents);

            $this->eventObserversByClient[$id] = new EventObserver($observeEvents, $ignoreCommandMonitoringEvents, $id, $this);
        }

        $this->entityMap->set($id, new Client($uri, $uriOptions));
    }

    private function createCollection(string $id, stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['id', 'database', 'collectionName', 'collectionOptions']);

        $collectionName = $o->collectionName ?? null;
        $databaseId = $o->database ?? null;

        assertInternalType('string', $collectionName);
        assertInternalType('string', $databaseId);

        $database = $this->entityMap[$databaseId];
        assertInstanceOf(Database::class, $database);

        $options = [];

        if (isset($o->collectionOptions)) {
            assertInternalType('object', $o->collectionOptions);
            $options = self::prepareCollectionOrDatabaseOptions((array) $o->collectionOptions);
        }

        $this->entityMap->set($id, $database->selectCollection($o->collectionName, $options), $databaseId);
    }

    private function createDatabase(string $id, stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['id', 'client', 'databaseName', 'databaseOptions']);

        $databaseName = $o->databaseName ?? null;
        $clientId = $o->client ?? null;

        assertInternalType('string', $databaseName);
        assertInternalType('string', $clientId);

        $client = $this->entityMap[$clientId];
        assertInstanceOf(Client::class, $client);

        $options = [];

        if (isset($o->databaseOptions)) {
            assertInternalType('object', $o->databaseOptions);
            $options = self::prepareCollectionOrDatabaseOptions((array) $o->databaseOptions);
        }

        $this->entityMap->set($id, $client->selectDatabase($databaseName, $options), $clientId);
    }

    private function createSession(string $id, stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['id', 'client', 'sessionOptions']);

        $clientId = $o->client ?? null;
        assertInternalType('string', $clientId);
        $client = $this->entityMap[$clientId];
        assertInstanceOf(Client::class, $client);

        $options = [];

        if (isset($o->sessionOptions)) {
            assertInternalType('object', $o->sessionOptions);
            $options = self::prepareSessionOptions((array) $o->sessionOptions);
        }

        $this->entityMap->set($id, $client->startSession($options), $clientId);
    }

    private function createBucket(string $id, stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['id', 'database', 'bucketOptions']);

        $databaseId = $o->database ?? null;
        assertInternalType('string', $databaseId);
        $database = $this->entityMap[$databaseId];
        assertInstanceOf(Database::class, $database);

        $options = [];

        if (isset($o->bucketOptions)) {
            assertInternalType('object', $o->bucketOptions);
            $options = self::prepareBucketOptions((array) $o->bucketOptions);
        }

        $this->entityMap->set($id, $database->selectGridFSBucket($options), $databaseId);
    }

    private function createStream(string $id, stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['id', 'hexBytes']);

        $hexBytes = $o->hexBytes ?? null;
        assertInternalType('string', $hexBytes);
        assertRegExp('/^([0-9a-fA-F]{2})*$/', $hexBytes);

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, hex2bin($hexBytes));
        rewind($stream);

        $this->entityMap->set($id, $stream);
    }

    private static function prepareCollectionOrDatabaseOptions(array $options) : array
    {
        Util::assertHasOnlyKeys($options, ['readConcern', 'readPreference', 'writeConcern']);

        return Util::prepareCommonOptions($options);
    }

    private static function prepareBucketOptions(array $options) : array
    {
        Util::assertHasOnlyKeys($options, ['bucketName', 'chunkSizeBytes', 'disableMD5', 'readConcern', 'readPreference', 'writeConcern']);

        if (array_key_exists('bucketName', $options)) {
            assertInternalType('string', $options['bucketName']);
        }

        if (array_key_exists('chunkSizeBytes', $options)) {
            assertInternalType('int', $options['chunkSizeBytes']);
        }

        if (array_key_exists('disableMD5', $options)) {
            assertInternalType('bool', $options['disableMD5']);
        }

        return Util::prepareCommonOptions($options);
    }

    private static function prepareSessionOptions(array $options) : array
    {
        Util::assertHasOnlyKeys($options, ['causalConsistency', 'defaultTransactionOptions']);

        if (array_key_exists('causalConsistency', $options)) {
            assertInternalType('bool', $options['causalConsistency']);
        }

        if (array_key_exists('defaultTransactionOptions', $options)) {
            assertInternalType('object', $options['defaultTransactionOptions']);
            $options['defaultTransactionOptions'] = self::prepareDefaultTransactionOptions((array) $options['defaultTransactionOptions']);
        }

        return $options;
    }

    private static function prepareDefaultTransactionOptions(array $options) : array
    {
        Util::assertHasOnlyKeys($options, ['maxCommitTimeMS', 'readConcern', 'readPreference', 'writeConcern']);

        if (array_key_exists('maxCommitTimeMS', $options)) {
            assertInternalType('int', $options['maxCommitTimeMS']);
        }

        return Util::prepareCommonOptions($options);
    }

    /**
     * Removes mongos hosts beyond the first if the URI refers to a sharded
     * cluster. Otherwise, the URI is returned as-is.
     */
    private static function removeMultipleMongoses(string $uri) : string
    {
        assertStringStartsWith('mongodb://', $uri);

        $manager = new Manager($uri);

        // Nothing to do if the URI does not refer to a sharded cluster
        if ($manager->selectServer(new ReadPreference(ReadPreference::PRIMARY))->getType() !== Server::TYPE_MONGOS) {
            return $uri;
        }

        $parts = parse_url($uri);

        assertInternalType('array', $parts);

        $hosts = explode(',', $parts['host']);

        // Nothing to do if the URI already has a single mongos host
        if (count($hosts) === 1) {
            return $uri;
        }

        // Re-append port to last host
        if (isset($parts['port'])) {
            $hosts[count($hosts) - 1] .= ':' . $parts['port'];
        }

        $singleHost = $hosts[0];
        $multipleHosts = implode(',', $hosts);

        $pos = strpos($uri, $multipleHosts);

        assertNotFalse($pos);

        return substr_replace($uri, $singleHost, $pos, strlen($multipleHosts));
    }

    /**
     * Requires multiple mongos hosts if the URI refers to a sharded cluster.
     */
    private static function requireMultipleMongoses(string $uri)
    {
        assertStringStartsWith('mongodb://', $uri);

        $manager = new Manager($uri);

        // Nothing to do if the URI does not refer to a sharded cluster
        if ($manager->selectServer(new ReadPreference(ReadPreference::PRIMARY))->getType() !== Server::TYPE_MONGOS) {
            return;
        }

        assertStringContains(',', parse_url($uri, PHP_URL_HOST));
    }
}
