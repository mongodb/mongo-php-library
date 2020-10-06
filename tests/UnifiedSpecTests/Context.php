<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Collection;
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
use function assertStringStartsWith;
use function count;
use function current;
use function explode;
use function implode;
use function key;
use function parse_url;
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
                    $this->entityMap[$id] = $this->createClient($def);
                    break;

                case 'database':
                    $this->entityMap[$id] = $this->createDatabase($def);
                    break;

                case 'collection':
                    $this->entityMap[$id] = $this->createCollection($def);
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

    private function createClient(stdClass $o) : Client
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

            $this->eventObserversByClient[$o->id] = new EventObserver($observeEvents, $ignoreCommandMonitoringEvents, $o->id, $this->entityMap);
        }

        return new Client($uri, $uriOptions);
    }

    private function createCollection(stdClass $o) : Collection
    {
        Util::assertHasOnlyKeys($o, ['id', 'database', 'collectionName', 'collectionOptions']);

        $collectionName = $o->collectionName ?? null;
        $database = $o->database ?? null;

        assertInternalType('string', $collectionName);
        assertInternalType('string', $database);

        $database = $this->entityMap[$database];
        assertInstanceOf(Database::class, $database);

        $options = [];

        if (isset($o->collectionOptions)) {
            assertInternalType('object', $o->collectionOptions);
            $options = self::prepareCollectionOrDatabaseOptions((array) $o->collectionOptions);
        }

        return $database->selectCollection($o->collectionName, $options);
    }

    private function createDatabase(stdClass $o) : Database
    {
        Util::assertHasOnlyKeys($o, ['id', 'client', 'databaseName', 'databaseOptions']);

        $databaseName = $o->databaseName ?? null;
        $client = $o->client ?? null;

        assertInternalType('string', $databaseName);
        assertInternalType('string', $client);

        $client = $this->entityMap[$client];
        assertInstanceOf(Client::class, $client);

        $options = [];

        if (isset($o->databaseOptions)) {
            assertInternalType('object', $o->databaseOptions);
            $options = self::prepareCollectionOrDatabaseOptions((array) $o->databaseOptions);
        }

        return $client->selectDatabase($databaseName, $options);
    }

    private static function prepareCollectionOrDatabaseOptions(array $options) : array
    {
        Util::assertHasOnlyKeys($options, ['readConcern', 'readPreference', 'writeConcern']);

        if (array_key_exists('readConcern', $options)) {
            assertInternalType('object', $options['readConcern']);
            $options['readConcern'] = Util::createReadConcern($options['readConcern']);
        }

        if (array_key_exists('readPreference', $options)) {
            assertInternalType('object', $options['readPreference']);
            $options['readPreference'] = Util::createReadPreference($options['readPreference']);
        }

        if (array_key_exists('writeConcern', $options)) {
            assertInternalType('object', $options['writeConcern']);
            $options['writeConcern'] = Util::createWriteConcern($options['writeConcern']);
        }

        return $options;
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
