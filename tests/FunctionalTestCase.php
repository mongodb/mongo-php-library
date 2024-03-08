<?php

namespace MongoDB\Tests;

use InvalidArgumentException;
use MongoDB\BSON\ObjectId;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\ServerApi;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\ListCollections;
use stdClass;
use UnexpectedValueException;

use function array_intersect_key;
use function call_user_func;
use function count;
use function current;
use function explode;
use function filter_var;
use function getenv;
use function implode;
use function in_array;
use function is_array;
use function is_callable;
use function is_executable;
use function is_object;
use function is_readable;
use function is_string;
use function key;
use function ob_get_clean;
use function ob_start;
use function parse_url;
use function phpinfo;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function version_compare;

use const DIRECTORY_SEPARATOR;
use const FILTER_VALIDATE_BOOLEAN;
use const INFO_MODULES;
use const PATH_SEPARATOR;

abstract class FunctionalTestCase extends TestCase
{
    private const ATLAS_TLD = '/\.(mongodb\.net|mongodb-dev\.net)/';

    protected Manager $manager;

    private array $configuredFailPoints = [];

    /** @var array{int,{Collection,array}} */
    private array $collectionsToCleanup = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = static::createTestManager();
        $this->configuredFailPoints = [];
    }

    public function tearDown(): void
    {
        if (! $this->hasFailed()) {
            $this->cleanupCollections();
        }

        $this->disableFailPoints();

        parent::tearDown();
    }

    public static function createTestClient(?string $uri = null, array $options = [], array $driverOptions = []): Client
    {
        return new Client(
            $uri ?? static::getUri(),
            static::appendAuthenticationOptions($options),
            static::appendServerApiOption($driverOptions),
        );
    }

    public static function createTestManager(?string $uri = null, array $options = [], array $driverOptions = []): Manager
    {
        return new Manager(
            $uri ?? static::getUri(),
            static::appendAuthenticationOptions($options),
            static::appendServerApiOption($driverOptions),
        );
    }

    public static function getUri($allowMultipleMongoses = false): string
    {
        /* If multiple mongoses are allowed, the multi-mongos load balanced URI
         * can be used if available; otherwise, fall back MONGODB_URI. */
        if ($allowMultipleMongoses) {
            return getenv('MONGODB_MULTI_MONGOS_LB_URI') ?: parent::getUri();
        }

        /* If multiple mongoses are prohibited, the single-mongos load balanced
         * URI can be used if available; otherwise, we need to conditionally
         * process MONGODB_URI. */
        return getenv('MONGODB_SINGLE_MONGOS_LB_URI') ?: static::getUriWithoutMultipleMongoses();
    }

    protected function assertCollectionCount($namespace, $count): void
    {
        [$databaseName, $collectionName] = explode('.', $namespace, 2);

        $cursor = $this->manager->executeCommand($databaseName, new Command(['count' => $collectionName]));
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        $this->assertArrayHasKey('n', $document);
        $this->assertEquals($count, $document['n']);
    }

    /**
     * Asserts that a collection with the given name does not exist on the
     * server.
     *
     * $databaseName defaults to TestCase::getDatabaseName() if unspecified.
     */
    protected function assertCollectionDoesNotExist(string $collectionName, ?string $databaseName = null): void
    {
        if (! isset($databaseName)) {
            $databaseName = $this->getDatabaseName();
        }

        $operation = new ListCollections($this->getDatabaseName());
        $collections = $operation->execute($this->getPrimaryServer());

        $foundCollection = null;

        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                $foundCollection = $collection;
                break;
            }
        }

        $this->assertNull($foundCollection, sprintf('Collection %s exists', $collectionName));
    }

    /**
     * Asserts that a collection with the given name exists on the server.
     *
     * $databaseName defaults to TestCase::getDatabaseName() if unspecified.
     * An optional $callback may be provided, which should take a CollectionInfo
     * argument as its first and only parameter. If a CollectionInfo matching
     * the given name is found, it will be passed to the callback, which may
     * perform additional assertions.
     */
    protected function assertCollectionExists(string $collectionName, ?string $databaseName = null, ?callable $callback = null): void
    {
        if (! isset($databaseName)) {
            $databaseName = $this->getDatabaseName();
        }

        if ($callback !== null && ! is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $operation = new ListCollections($databaseName);
        $collections = $operation->execute($this->getPrimaryServer());

        $foundCollection = null;

        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                $foundCollection = $collection;
                break;
            }
        }

        $this->assertNotNull($foundCollection, sprintf('Found %s collection in the database', $collectionName));

        if ($callback !== null) {
            call_user_func($callback, $foundCollection);
        }
    }

    protected function assertCommandSucceeded($document): void
    {
        $document = is_object($document) ? (array) $document : $document;

        $this->assertArrayHasKey('ok', $document);
        $this->assertEquals(1, $document['ok']);
    }

    protected function assertSameObjectId($expectedObjectId, $actualObjectId): void
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
    public function configureFailPoint($command, ?Server $server = null): void
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

        if (key((array) $command) !== 'configureFailPoint') {
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

    public static function getModuleInfo(string $row): ?string
    {
        ob_start();
        phpinfo(INFO_MODULES);
        $info = ob_get_clean();

        $pattern = sprintf('/^%s(.*)$/m', preg_quote($row . ' => '));

        if (preg_match($pattern, $info, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Creates the test collection with the specified options and ensures it is
     * dropped again during tearDown(). If the collection already exists, it
     * is dropped and recreated.
     *
     * A majority write concern is applied by default to ensure that the
     * transaction can acquire the required locks.
     * See: https://www.mongodb.com/docs/manual/core/transactions/#transactions-and-operations
     *
     * @param array $options CreateCollection options
     */
    protected function createCollection(string $databaseName, string $collectionName, array $options = []): Collection
    {
        // See: https://jira.mongodb.org/browse/PHPLIB-1145
        if (isset($options['encryptedFields'])) {
            throw new InvalidArgumentException('The "encryptedFields" option is not supported by createCollection(). Time to refactor!');
        }

        // Pass only relevant options to drop the collection in case it already exists
        $dropOptions = array_intersect_key($options, ['writeConcern' => 1, 'encryptedFields' => 1]);
        $collection = $this->dropCollection($databaseName, $collectionName, $dropOptions);

        $options += ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
        $operation = new CreateCollection($databaseName, $collectionName, $options);
        $operation->execute($this->getPrimaryServer());

        return $collection;
    }

    /**
     * Drops the test collection and ensures it is dropped again during tearDown().
     *
     * A majority write concern is applied by default to ensure that the
     * transaction can acquire the required locks.
     * See: https://www.mongodb.com/docs/manual/core/transactions/#transactions-and-operations
     *
     * @param array $options Collection::dropCollection() options
     */
    protected function dropCollection(string $databaseName, string $collectionName, array $options = []): Collection
    {
        $collection = new Collection($this->manager, $databaseName, $collectionName);
        $this->collectionsToCleanup[] = [$collection, $options];

        $options += ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
        $collection->drop($options);

        return $collection;
    }

    private function cleanupCollections(): void
    {
        foreach ($this->collectionsToCleanup as [$collection, $options]) {
            $options += ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];
            $collection->drop($options);
        }

        $this->collectionsToCleanup = [];
    }

    protected function getFeatureCompatibilityVersion(?ReadPreference $readPreference = null)
    {
        if ($this->isShardedCluster()) {
            return $this->getServerVersion($readPreference);
        }

        $cursor = $this->manager->executeCommand(
            'admin',
            new Command(['getParameter' => 1, 'featureCompatibilityVersion' => 1]),
            $readPreference ?: new ReadPreference(ReadPreference::PRIMARY),
        );

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        if (isset($document['featureCompatibilityVersion']['version']) && is_string($document['featureCompatibilityVersion']['version'])) {
            return $document['featureCompatibilityVersion']['version'];
        }

        throw new UnexpectedValueException('Could not determine featureCompatibilityVersion');
    }

    protected function getPrimaryServer()
    {
        return $this->manager->selectServer();
    }

    protected function getServerVersion(?ReadPreference $readPreference = null)
    {
        $buildInfo = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(['buildInfo' => 1]),
            $readPreference ?: new ReadPreference(ReadPreference::PRIMARY),
        )->toArray()[0];

        if (isset($buildInfo->version) && is_string($buildInfo->version)) {
            return preg_replace('#^(\d+\.\d+\.\d+).*$#', '\1', $buildInfo->version);
        }

        throw new UnexpectedValueException('Could not determine server version');
    }

    protected function getServerStorageEngine(?ReadPreference $readPreference = null)
    {
        $cursor = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(['serverStatus' => 1]),
            $readPreference ?: new ReadPreference(ReadPreference::PRIMARY),
        );

        $result = current($cursor->toArray());

        if (isset($result->storageEngine->name) && is_string($result->storageEngine->name)) {
            return $result->storageEngine->name;
        }

        throw new UnexpectedValueException('Could not determine server storage engine');
    }

    /**
     * Returns whether clients must specify an API version by checking the
     * requireApiVersion server parameter.
     */
    protected function isApiVersionRequired(): bool
    {
        try {
            $cursor = $this->manager->executeCommand(
                'admin',
                new Command(['getParameter' => 1, 'requireApiVersion' => 1]),
            );

            $document = current($cursor->toArray());
        } catch (CommandException $e) {
            return false;
        }

        return isset($document->requireApiVersion) && $document->requireApiVersion === true;
    }

    protected function isLoadBalanced()
    {
        return $this->getPrimaryServer()->getType() == Server::TYPE_LOAD_BALANCER;
    }

    protected function isReplicaSet()
    {
        return $this->getPrimaryServer()->getType() == Server::TYPE_RS_PRIMARY;
    }

    protected function isMongos()
    {
        return $this->getPrimaryServer()->getType() == Server::TYPE_MONGOS;
    }

    protected function isStandalone()
    {
        return $this->getPrimaryServer()->getType() == Server::TYPE_STANDALONE;
    }

    /**
     * Return whether serverless (i.e. proxy as mongos) is being utilized.
     */
    protected static function isServerless(): bool
    {
        $isServerless = getenv('MONGODB_IS_SERVERLESS');

        return $isServerless !== false ? filter_var($isServerless, FILTER_VALIDATE_BOOLEAN) : false;
    }

    protected function isShardedCluster()
    {
        $type = $this->getPrimaryServer()->getType();

        if ($type == Server::TYPE_MONGOS) {
            return true;
        }

        // Assume that load balancers are properly configured and front sharded clusters
        if ($type == Server::TYPE_LOAD_BALANCER) {
            return true;
        }

        return false;
    }

    protected function skipIfServerVersion(string $operator, string $version, ?string $message = null): void
    {
        if (version_compare($this->getServerVersion(), $version, $operator)) {
            $this->markTestSkipped($message ?? sprintf('Server version is %s %s', $operator, $version));
        }
    }

    protected function skipIfChangeStreamIsNotSupported(): void
    {
        if ($this->isStandalone()) {
            $this->markTestSkipped('$changeStream requires replica sets');
        }
    }

    protected function skipIfCausalConsistencyIsNotSupported(): void
    {
        switch ($this->getPrimaryServer()->getType()) {
            case Server::TYPE_STANDALONE:
                $this->markTestSkipped('Causal consistency requires replica sets');
                break;

            case Server::TYPE_RS_PRIMARY:
                // Note: mongos does not report storage engine information
                if ($this->getServerStorageEngine() !== 'wiredTiger') {
                    $this->markTestSkipped('Causal consistency requires WiredTiger storage engine');
                }

                break;
        }
    }

    protected function skipIfClientSideEncryptionIsNotSupported(): void
    {
        if (version_compare($this->getFeatureCompatibilityVersion(), '4.2', '<')) {
            $this->markTestSkipped('Client Side Encryption only supported on FCV 4.2 or higher');
        }

        if (static::getModuleInfo('libmongocrypt') === 'disabled') {
            $this->markTestSkipped('Client Side Encryption is not enabled in the MongoDB extension');
        }

        if (! static::isCryptSharedLibAvailable() && ! static::isMongocryptdAvailable()) {
            $this->markTestSkipped('Neither crypt_shared nor mongocryptd are available');
        }
    }

    protected function skipIfGeoHaystackIndexIsNotSupported(): void
    {
        if (version_compare($this->getServerVersion(), '4.9', '>=')) {
            $this->markTestSkipped('GeoHaystack indexes cannot be created in version 4.9 and above');
        }
    }

    protected function skipIfTransactionsAreNotSupported(): void
    {
        if ($this->getPrimaryServer()->getType() === Server::TYPE_STANDALONE) {
            $this->markTestSkipped('Transactions are not supported on standalone servers');
        }

        if ($this->isShardedCluster()) {
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

    protected function isAtlasDataLake(): bool
    {
        $buildInfo = $this->getPrimaryServer()->executeCommand(
            $this->getDatabaseName(),
            new Command(['buildInfo' => 1]),
        )->toArray()[0];

        return ! empty($buildInfo->dataLake);
    }

    protected function isEnterprise(): bool
    {
        $buildInfo = $this->getPrimaryServer()->executeCommand(
            $this->getDatabaseName(),
            new Command(['buildInfo' => 1]),
        )->toArray()[0];

        if (isset($buildInfo->modules) && is_array($buildInfo->modules)) {
            return in_array('enterprise', $buildInfo->modules);
        }

        throw new UnexpectedValueException('Could not determine server modules');
    }

    public static function isAtlas(?string $uri = null): bool
    {
        return preg_match(self::ATLAS_TLD, $uri ?? static::getUri());
    }

    /** @see https://www.mongodb.com/docs/manual/core/queryable-encryption/reference/shared-library/ */
    public static function isCryptSharedLibAvailable(): bool
    {
        $cryptSharedLibPath = getenv('CRYPT_SHARED_LIB_PATH');

        if ($cryptSharedLibPath === false) {
            return false;
        }

        return is_readable($cryptSharedLibPath);
    }

    /** @see https://www.mongodb.com/docs/manual/core/queryable-encryption/reference/mongocryptd/ */
    public static function isMongocryptdAvailable(): bool
    {
        $paths = explode(PATH_SEPARATOR, getenv('PATH'));

        foreach ($paths as $path) {
            if (is_executable($path . DIRECTORY_SEPARATOR . 'mongocryptd')) {
                return true;
            }
        }

        return false;
    }

    private static function appendAuthenticationOptions(array $options): array
    {
        if (isset($options['username']) || isset($options['password'])) {
            return $options;
        }

        $username = getenv('MONGODB_USERNAME') ?: null;
        $password = getenv('MONGODB_PASSWORD') ?: null;

        if ($username !== null) {
            $options['username'] = $username;
        }

        if ($password !== null) {
            $options['password'] = $password;
        }

        return $options;
    }

    private static function appendServerApiOption(array $driverOptions): array
    {
        if (getenv('API_VERSION') && ! isset($driverOptions['serverApi'])) {
            $driverOptions['serverApi'] = new ServerApi(getenv('API_VERSION'));
        }

        return $driverOptions;
    }

    /**
     * Disables any fail points that were configured earlier in the test.
     *
     * This tracks fail points set via configureFailPoint() and should be called
     * during tearDown().
     */
    private function disableFailPoints(): void
    {
        if (empty($this->configuredFailPoints)) {
            return;
        }

        foreach ($this->configuredFailPoints as [$failPoint, $server]) {
            $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
            $operation->execute($server);
        }
    }

    private static function getUriWithoutMultipleMongoses(): string
    {
        /* Cache the result. We can safely assume the topology type will remain
         * constant for the duration of the test suite. */
        static $uri;

        if (isset($uri)) {
            return $uri;
        }

        $uri = parent::getUri();
        $parsed = parse_url($uri);

        if (! isset($parsed['scheme'], $parsed['host'])) {
            throw new UnexpectedValueException('Failed to parse scheme and host components from URI: ' . $uri);
        }

        // Only modify URIs using the mongodb scheme
        if ($parsed['scheme'] !== 'mongodb') {
            return $uri;
        }

        $hosts = explode(',', $parsed['host']);
        $numHosts = count($hosts);

        if ($numHosts === 1) {
            return $uri;
        }

        $manager = static::createTestManager($uri);
        if ($manager->selectServer()->getType() !== Server::TYPE_MONGOS) {
            return $uri;
        }

        // Re-append port to last host
        if (isset($parsed['port'])) {
            $hosts[$numHosts - 1] .= ':' . $parsed['port'];
        }

        $parts = ['mongodb://'];

        if (isset($parsed['user'], $parsed['pass'])) {
            $parts[] = $parsed['user'] . ':' . $parsed['pass'] . '@';
        }

        $parts[] = $hosts[0];

        if (isset($parsed['path'])) {
            $parts[] = $parsed['path'];
        } elseif (isset($parsed['query'])) {
            /* URIs containing connection options but no auth database component
             * still require a slash before the question mark */
            $parts[] = '/';
        }

        if (isset($parsed['query'])) {
            $parts[] = '?' . $parsed['query'];
        }

        $uri = implode('', $parts);

        return $uri;
    }

    /**
     * Checks if the failCommand command is supported on this server version
     */
    private function isFailCommandSupported(): bool
    {
        $minVersion = $this->isShardedCluster() ? '4.1.5' : '4.0.0';

        return version_compare($this->getServerVersion(), $minVersion, '>=');
    }

    /**
     * Checks if the failCommand command is enabled by checking the enableTestCommands parameter
     */
    private function isFailCommandEnabled(): bool
    {
        try {
            $cursor = $this->manager->executeCommand(
                'admin',
                new Command(['getParameter' => 1, 'enableTestCommands' => 1]),
            );

            $document = current($cursor->toArray());
        } catch (CommandException $e) {
            return false;
        }

        return isset($document->enableTestCommands) && $document->enableTestCommands === true;
    }
}
