<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use stdClass;
use function array_diff_key;
use function array_keys;
use function getenv;
use function implode;
use function mt_rand;
use function uniqid;
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

    /** @var string */
    private $uri;

    public function __construct(string $uri)
    {
        $this->entityMap = new EntityMap;
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
            assertIsObject($entity);
            $entity = (array) $entity;
            assertCount(1, $entity);

            $type = key($entity);
            $def = current($entity);
            assertIsObject($def);

            $id = $def->id ?? null;
            assertIsString($id);
            assertArrayNotHasKey($id, $this->entityMap);

            switch ($type) {
                case 'client':
                    $this->entityMap[$id] = $this->createClient($def);

                    if (isset($def->observeEvents)) {
                        $this->eventObserversByClient[$id] = $this->createEventObserver($def);  
                    }
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

    private function createClient(stdClass $o): Client
    {
        $uri = $this->uri;

        if (isset($o->useMultipleMongoses)) {
            assertIsBool($o->useMultipleMongoses);

            if ($o->useMultipleMongoses) {
                self::requireMultipleMongoses($uri);
            } else {
                $uri = self::removeMultipleMongoses($uri);
            }
        }

        $uriOptions = [];

        if (isset($o->uriOptions)) {
            assertIsObject($o->uriOptions);
            $uriOptions = (array) $o->uriOptions;
        }

        return new Client($uri, $uriOptions);
    }

    private function createEventObserver(stdClass $o): EventObserver
    {
        $observeEvents = $o->observeEvents ?? null;
        $ignoreCommands = $o->ignoreCommandMonitoringEvents ?? [];

        assertIsArray($observeEvents);
        assertIsArray($ignoreCommands);

        return new EventObserver($observeEvents, $ignoreCommands);
    }

    private function createCollection(stdClass $o): Collection
    {
        $collectionName = $o->collectionName ?? null;
        $database = $o->database ?? null;

        assertIsString($collectionName);
        assertIsString($database);
        assertArrayHasKey($database, $this->entityMap);

        $database = $this->entityMap[$o->database];
        assertInstanceOf(Database::class, $database);

        $options = isset($o->collectionOptions) ? self::prepareCollectionOrDatabaseOptions($o->collectionOptions) : [];

        return $database->selectCollection($o->collectionName, $options);
    }

    private function createDatabase(stdClass $o): Database
    {
        assertObjectHasAttribute('databaseName', $o);
        assertIsString($o->databaseName);

        assertObjectHasAttribute('client', $o);
        assertIsString($o->client);
        assertArrayHasKey($o->client, $this->entityMap);

        $client = $this->entityMap[$o->client];
        assertInstanceOf(Client::class, $client);

        $options = isset($o->databaseOptions) ? self::prepareCollectionOrDatabaseOptions($o->databaseOptions) : [];

        return $client->selectDatabase($o->databaseName, $options);
    }

    private static function prepareCollectionOrDatabaseOptions(stdClass $o): array
    {
        $options = [];

        if (isset($o->readConcern)) {
            assertIsObject($o->readConcern);
            $options['readConcern'] = self::prepareReadConcern($o->readConcern);
        }

        if (isset($o->readPreference)) {
            assertIsObject($o->readPreference);
            $options['readPreference'] = self::prepareReadPreference($o->readPreference);
        }

        if (isset($o->writeConcern)) {
            assertIsObject($o->writeConcern);
            $options['writeConcern'] = self::prepareWriteConcern($o->writeConcern);
        }

        return $options;
    }

    private static function createReadConcern(stdClass $o): ReadConcern
    {
        $level = $o->level ?? null;
        assertIsString($level);

        return new ReadConcern($level);
    }

    private static function createReadPreference(stdClass $o): ReadPreference
    {
        $mode = $o->mode ?? null;
        $tagSets = $o->tagSets ?? null;
        $maxStalenessSeconds = $o->maxStalenessSeconds ?? null;
        $hedge = $o->hedge ?? null;

        assertIsString($mode);

        if (isset($tagSets)) {
            assertIsArray($tagSets);
            assertContains('object', $tagSets);
        }

        $options = [];

        if (isset($maxStalenessSeconds)) {
            assertIsInt($maxStalenessSeconds);
            $options['maxStalenessSeconds'] = $maxStalenessSeconds;
        }

        if (isset($hedge)) {
            assertIsObject($hedge);
            $options['hedge'] = $hedge;
        }

        return new ReadPreference($mode, $tagSets, $options);
    }

    private static function createWriteConcern(stdClass $o): WriteConcern
    {
        $w = $o->w ?? -2 /* MONGOC_WRITE_CONCERN_W_DEFAULT */;
        $wtimeoutMS = $o->wtimeoutMS ?? 0;
        $journal = $o->journal ?? null;

        assertThat($w, logicalOr(new IsType('int'), new IsType('string')));
        assertIsInt($wtimeoutMS);

        $args = [$w, $wtimeoutMS];

        if (isset($journal)) {
            assertIsBool($journal);
            $args[] = $journal;
        }

        return new WriteConcern(...$args);
    }

    /**
     * Removes mongos hosts beyond the first if the URI refers to a sharded
     * cluster. Otherwise, the URI is returned as-is.
     */
    private static function removeMultipleMongoses(string $uri): string
    {
        assertStringStartsWith('mongodb://', $uri);

        $manager = new Manager($uri);

        // Nothing to do if the URI does not refer to a sharded cluster
        if ($manager->selectServer(new ReadPreference(ReadPreference::PRIMARY))->getType() !== Server::TYPE_MONGOS) {
            return $uri;
        }

        $parts = parse_url($uri);

        assertIsArray($parts);

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
