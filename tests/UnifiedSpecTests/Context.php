<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\ServerApi;
use MongoDB\Model\BSONArray;
use MongoDB\Tests\FunctionalTestCase;
use stdClass;

use function array_key_exists;
use function array_map;
use function count;
use function current;
use function explode;
use function implode;
use function key;
use function parse_url;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsBool;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotFalse;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringStartsWith;
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
    /** @var string */
    private $activeClient;

    /** @var EntityMap */
    private $entityMap;

    /** @var EventCollector[] */
    private $eventCollectors = [];

    /** @var EventObserver[] */
    private $eventObserversByClient = [];

    /** @var Client */
    private $internalClient;

    /** @var boolean */
    private $inLoop = false;

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
    public function createEntities(array $entities): void
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

                default:
                    throw new LogicException('Unsupported entity type: ' . $type);
            }
        }
    }

    public function getEntityMap(): EntityMap
    {
        return $this->entityMap;
    }

    public function getInternalClient(): Client
    {
        return $this->internalClient;
    }

    public function isActiveClient(string $clientId): bool
    {
        return $this->activeClient === $clientId;
    }

    public function setActiveClient(?string $clientId = null): void
    {
        $this->activeClient = $clientId;
    }

    public function isInLoop(): bool
    {
        return $this->inLoop;
    }

    public function setInLoop(bool $inLoop): void
    {
        $this->inLoop = $inLoop;
    }

    public function assertExpectedEventsForClients(array $expectedEventsForClients): void
    {
        assertNotEmpty($expectedEventsForClients);

        foreach ($expectedEventsForClients as $expectedEventsForClient) {
            assertIsObject($expectedEventsForClient);
            Util::assertHasOnlyKeys($expectedEventsForClient, ['client', 'events', 'eventType']);

            $client = $expectedEventsForClient->client ?? null;
            $eventType = $expectedEventsForClient->eventType ?? 'command';
            $expectedEvents = $expectedEventsForClient->events ?? null;

            assertIsString($client);
            assertArrayHasKey($client, $this->eventObserversByClient);
            /* Note: PHPC does not implement CMAP. Any tests expecting CMAP
             * events should be skipped explicitly. */
            assertSame('command', $eventType);
            assertIsArray($expectedEvents);

            $this->eventObserversByClient[$client]->assert($expectedEvents);
        }
    }

    public function startEventObservers(): void
    {
        foreach ($this->eventObserversByClient as $eventObserver) {
            $eventObserver->start();
        }
    }

    public function stopEventObservers(): void
    {
        foreach ($this->eventObserversByClient as $eventObserver) {
            $eventObserver->stop();
        }
    }

    public function getEventObserverForClient(string $id): EventObserver
    {
        assertArrayHasKey($id, $this->eventObserversByClient);

        return $this->eventObserversByClient[$id];
    }

    public function startEventCollectors(): void
    {
        foreach ($this->eventCollectors as $eventCollector) {
            $eventCollector->start();
        }
    }

    public function stopEventCollectors(): void
    {
        foreach ($this->eventCollectors as $eventCollector) {
            $eventCollector->stop();
        }
    }

    /** @param string|array $readPreferenceTags */
    private function convertReadPreferenceTags($readPreferenceTags): array
    {
        return array_map(
            static function (string $readPreferenceTagSet): array {
                $tags = explode(',', $readPreferenceTagSet);

                return array_map(
                    static function (string $tag): array {
                        [$key, $value] = explode(':', $tag);

                        return [$key => $value];
                    },
                    $tags
                );
            },
            (array) $readPreferenceTags
        );
    }

    private function createClient(string $id, stdClass $o): void
    {
        Util::assertHasOnlyKeys($o, [
            'id',
            'uriOptions',
            'useMultipleMongoses',
            'observeEvents',
            'ignoreCommandMonitoringEvents',
            'observeSensitiveCommands',
            'serverApi',
            'storeEventsAsEntities',
        ]);

        $useMultipleMongoses = $o->useMultipleMongoses ?? null;
        $observeEvents = $o->observeEvents ?? null;
        $ignoreCommandMonitoringEvents = $o->ignoreCommandMonitoringEvents ?? [];
        $observeSensitiveCommands = $o->observeSensitiveCommands ?? false;
        $serverApi = $o->serverApi ?? null;
        $storeEventsAsEntities = $o->storeEventsAsEntities ?? null;

        $uri = $this->uri;

        if (isset($useMultipleMongoses)) {
            assertIsBool($useMultipleMongoses);

            if ($useMultipleMongoses) {
                self::requireMultipleMongoses($uri);
            } else {
                $uri = self::removeMultipleMongoses($uri);
            }
        }

        $uriOptions = [];

        if (isset($o->uriOptions)) {
            assertIsObject($o->uriOptions);
            $uriOptions = (array) $o->uriOptions;

            if (! empty($uriOptions['readPreferenceTags'])) {
                /* readPreferenceTags may take the following form:
                 *
                 * 1. A string containing multiple tags: "dc:ny,rack:1".
                 *    Expected result: [["dc" => "ny", "rack" => "1"]]
                 * 2. An array containing multiple strings as above: ["dc:ny,rack:1", "dc:la"].
                 *    Expected result: [["dc" => "ny", "rack" => "1"], ["dc" => "la"]]
                 */
                $uriOptions['readPreferenceTags'] = $this->convertReadPreferenceTags($uriOptions['readPreferenceTags']);
            }
        }

        if (isset($observeEvents)) {
            assertIsArray($observeEvents);
            assertIsArray($ignoreCommandMonitoringEvents);
            assertIsBool($observeSensitiveCommands);

            $this->eventObserversByClient[$id] = new EventObserver($observeEvents, $ignoreCommandMonitoringEvents, $observeSensitiveCommands, $id, $this);
        }

        if (isset($storeEventsAsEntities)) {
            assertIsArray($storeEventsAsEntities);

            foreach ($storeEventsAsEntities as $storeEventsAsEntity) {
                $this->createEntityCollector($id, $storeEventsAsEntity);
            }
        }

        /* TODO: Remove this once PHPC-1645 is implemented. Each client needs
         * its own libmongoc client to facilitate txnNumber assertions. */
        static $i = 0;
        $driverOptions = isset($observeEvents) ? ['i' => $i++] : [];

        if ($serverApi !== null) {
            assertIsObject($serverApi);
            $driverOptions['serverApi'] = new ServerApi(
                $serverApi->version,
                $serverApi->strict ?? null,
                $serverApi->deprecationErrors ?? null
            );
        }

        $this->entityMap->set($id, FunctionalTestCase::createTestClient($uri, $uriOptions, $driverOptions));
    }

    private function createEntityCollector(string $clientId, stdClass $o): void
    {
        Util::assertHasOnlyKeys($o, ['id', 'events']);

        $eventListId = $o->id ?? null;
        $events = $o->events ?? null;

        assertNotSame($eventListId, $clientId);
        assertIsArray($events);

        $eventList = new BSONArray();
        $this->entityMap->set($eventListId, $eventList);
        $this->eventCollectors[] = new EventCollector($eventList, $events, $clientId, $this);
    }

    private function createCollection(string $id, stdClass $o): void
    {
        Util::assertHasOnlyKeys($o, ['id', 'database', 'collectionName', 'collectionOptions']);

        $collectionName = $o->collectionName ?? null;
        $databaseId = $o->database ?? null;

        assertIsString($collectionName);
        assertIsString($databaseId);

        $database = $this->entityMap->getDatabase($databaseId);

        $options = [];

        if (isset($o->collectionOptions)) {
            assertIsObject($o->collectionOptions);
            $options = self::prepareCollectionOrDatabaseOptions((array) $o->collectionOptions);
        }

        $this->entityMap->set($id, $database->selectCollection($o->collectionName, $options), $databaseId);
    }

    private function createDatabase(string $id, stdClass $o): void
    {
        Util::assertHasOnlyKeys($o, ['id', 'client', 'databaseName', 'databaseOptions']);

        $databaseName = $o->databaseName ?? null;
        $clientId = $o->client ?? null;

        assertIsString($databaseName);
        assertIsString($clientId);

        $client = $this->entityMap->getClient($clientId);

        $options = [];

        if (isset($o->databaseOptions)) {
            assertIsObject($o->databaseOptions);
            $options = self::prepareCollectionOrDatabaseOptions((array) $o->databaseOptions);
        }

        $this->entityMap->set($id, $client->selectDatabase($databaseName, $options), $clientId);
    }

    private function createSession(string $id, stdClass $o): void
    {
        Util::assertHasOnlyKeys($o, ['id', 'client', 'sessionOptions']);

        $clientId = $o->client ?? null;
        assertIsString($clientId);
        $client = $this->entityMap->getClient($clientId);

        $options = [];

        if (isset($o->sessionOptions)) {
            assertIsObject($o->sessionOptions);
            $options = self::prepareSessionOptions((array) $o->sessionOptions);
        }

        $this->entityMap->set($id, $client->startSession($options), $clientId);
    }

    private function createBucket(string $id, stdClass $o): void
    {
        Util::assertHasOnlyKeys($o, ['id', 'database', 'bucketOptions']);

        $databaseId = $o->database ?? null;
        assertIsString($databaseId);
        $database = $this->entityMap->getDatabase($databaseId);

        $options = [];

        if (isset($o->bucketOptions)) {
            assertIsObject($o->bucketOptions);
            $options = self::prepareBucketOptions((array) $o->bucketOptions);
        }

        $this->entityMap->set($id, $database->selectGridFSBucket($options), $databaseId);
    }

    private static function prepareCollectionOrDatabaseOptions(array $options): array
    {
        Util::assertHasOnlyKeys($options, ['readConcern', 'readPreference', 'writeConcern']);

        return Util::prepareCommonOptions($options);
    }

    private static function prepareBucketOptions(array $options): array
    {
        Util::assertHasOnlyKeys($options, ['bucketName', 'chunkSizeBytes', 'disableMD5', 'readConcern', 'readPreference', 'writeConcern']);

        if (array_key_exists('bucketName', $options)) {
            assertIsString($options['bucketName']);
        }

        if (array_key_exists('chunkSizeBytes', $options)) {
            assertIsInt($options['chunkSizeBytes']);
        }

        if (array_key_exists('disableMD5', $options)) {
            assertIsBool($options['disableMD5']);
        }

        return Util::prepareCommonOptions($options);
    }

    private static function prepareSessionOptions(array $options): array
    {
        Util::assertHasOnlyKeys($options, ['causalConsistency', 'defaultTransactionOptions', 'snapshot']);

        if (array_key_exists('causalConsistency', $options)) {
            assertIsBool($options['causalConsistency']);
        }

        if (array_key_exists('defaultTransactionOptions', $options)) {
            assertIsObject($options['defaultTransactionOptions']);
            $options['defaultTransactionOptions'] = self::prepareDefaultTransactionOptions((array) $options['defaultTransactionOptions']);
        }

        if (array_key_exists('snapshot', $options)) {
            assertIsBool($options['snapshot']);
        }

        return $options;
    }

    private static function prepareDefaultTransactionOptions(array $options): array
    {
        Util::assertHasOnlyKeys($options, ['maxCommitTimeMS', 'readConcern', 'readPreference', 'writeConcern']);

        if (array_key_exists('maxCommitTimeMS', $options)) {
            assertIsInt($options['maxCommitTimeMS']);
        }

        return Util::prepareCommonOptions($options);
    }

    /**
     * Removes mongos hosts beyond the first if the URI refers to a sharded
     * cluster. Otherwise, the URI is returned as-is.
     */
    private static function removeMultipleMongoses(string $uri): string
    {
        assertStringStartsWith('mongodb://', $uri);

        $manager = FunctionalTestCase::createTestManager($uri);

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
    private static function requireMultipleMongoses(string $uri): void
    {
        assertStringStartsWith('mongodb://', $uri);

        $manager = FunctionalTestCase::createTestManager($uri);

        // Nothing to do if the URI does not refer to a sharded cluster
        if ($manager->selectServer(new ReadPreference(ReadPreference::PRIMARY))->getType() !== Server::TYPE_MONGOS) {
            return;
        }

        assertStringContainsString(',', parse_url($uri, PHP_URL_HOST));
    }
}
