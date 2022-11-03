<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Collection;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Model\BSONArray;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Tests\FunctionalTestCase;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\IncompleteTest;
use PHPUnit\Framework\SkippedTest;
use PHPUnit\Framework\Warning;
use stdClass;
use Throwable;
use UnexpectedValueException;

use function call_user_func;
use function count;
use function explode;
use function filter_var;
use function gc_collect_cycles;
use function getenv;
use function implode;
use function in_array;
use function is_executable;
use function is_readable;
use function is_string;
use function parse_url;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotFalse;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringStartsWith;
use function preg_match;
use function preg_replace;
use function sprintf;
use function strlen;
use function strpos;
use function substr_replace;
use function version_compare;

use const DIRECTORY_SEPARATOR;
use const FILTER_VALIDATE_BOOLEAN;
use const PATH_SEPARATOR;
use const PHP_URL_HOST;

/**
 * Unified test runner.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst
 */
final class UnifiedTestRunner
{
    public const ATLAS_TLD = 'mongodb.net';

    public const SERVER_ERROR_INTERRUPTED = 11601;
    public const SERVER_ERROR_UNAUTHORIZED = 13;

    public const MIN_SCHEMA_VERSION = '1.0';

    /* Note: This is necessary to support expectedError.errorResponse from 1.12;
     * however, syntax from 1.9, 1.10, and 1.11 has not been implemented. */
    public const MAX_SCHEMA_VERSION = '1.12';

    /** @var MongoDB\Client */
    private $internalClient;

    /** @var string */
    private $internalClientUri;

    /** @var bool */
    private $allowKillAllSessions = true;

    /** @var EntityMap */
    private $entityMap;

    /** @var callable(EntityMap):void */
    private $entityMapObserver;

    /** @var FailPointObserver */
    private $failPointObserver;

    /** @var ServerParameterHelper */
    private $serverParameterHelper;

    public function __construct(string $internalClientUri)
    {
        $this->internalClient = FunctionalTestCase::createTestClient($internalClientUri);
        $this->internalClientUri = $internalClientUri;

        /* Atlas prohibits killAllSessions. Inspect the connection string to
         * determine if we should avoid calling killAllSessions(). This does
         * mean that lingering transactions could block test execution. */
        if ($this->isServerless() || strpos($internalClientUri, self::ATLAS_TLD) !== false) {
            $this->allowKillAllSessions = false;
        }

        $this->serverParameterHelper = new ServerParameterHelper($this->internalClient);
    }

    public function run(UnifiedTestCase $test): void
    {
        $this->doSetUp();
        $hasFailed = false;

        try {
            $this->doTestCase(...$test);
        } catch (Throwable $e) {
            /* As is done in PHPUnit\Framework\TestCase::runBare(), exceptions
             * other than a select few will indicate a test failure. We cannot
             * call TestCase::hasFailed() for two reasons: runBare() has yet to
             * catch the exceptions and update the TestCase's status and, more
             * importantly, this class does not have access to the TestCase. */
            $hasFailed = ! ($e instanceof IncompleteTest || $e instanceof SkippedTest || $e instanceof Warning);

            throw $e;
        } finally {
            /* An EntityMap observer should be invoked irrespective of the test
             * succeeding or failing. Since the callable itself might throw, we
             * need to ensure doTearDown() will still be called. */
            try {
                if (isset($this->entityMapObserver, $this->entityMap)) {
                    call_user_func($this->entityMapObserver, $this->entityMap);
                }
            } finally {
                $this->doTearDown($hasFailed);
            }
        }
    }

    /**
     * Defines a callable to receive the EntityMap after each test.
     *
     * This function is primarily used by the Atlas testing workload executor.
     *
     * @param callable(EntityMap):void $entityMapObserver
     */
    public function setEntityMapObserver(callable $entityMapObserver): void
    {
        $this->entityMapObserver = $entityMapObserver;
    }

    private function doSetUp(): void
    {
        /* The transactions spec advises calling killAllSessions only at the
         * start of the test suite and after failed tests; however, the "unpin
         * after transient error within a transaction" pinning test causes the
         * subsequent transaction test to block. */
        $this->killAllSessions();

        $this->failPointObserver = new FailPointObserver();
        $this->failPointObserver->start();
    }

    private function doTearDown(bool $hasFailed): void
    {
        $this->entityMap = null;

        if ($hasFailed) {
            $this->killAllSessions();
        }

        $this->failPointObserver->stop();
        $this->failPointObserver->disableFailPoints();

        /* Manually invoking garbage collection since each test is prone to
         * create cycles (perhaps due to EntityMap), which can leak and prevent
         * sessions from being released back into the pool. */
        gc_collect_cycles();
    }

    private function doTestCase(stdClass $test, string $schemaVersion, ?array $runOnRequirements = null, ?array $createEntities = null, ?array $initialData = null): void
    {
        if (! $this->isSchemaVersionSupported($schemaVersion)) {
            Assert::markTestIncomplete(sprintf('Test format schema version "%s" is not supported', $schemaVersion));
        }

        if (isset($runOnRequirements)) {
            $this->checkRunOnRequirements($runOnRequirements);
        }

        if (isset($test->skipReason)) {
            assertIsString($test->skipReason);
            Assert::markTestSkipped($test->skipReason);
        }

        if (isset($test->runOnRequirements)) {
            assertIsArray($test->runOnRequirements);
            $this->checkRunOnRequirements($test->runOnRequirements);
        }

        if (isset($initialData)) {
            $this->prepareInitialData($initialData);
        }

        $context = $this->createContext();

        /* If an EntityMap observer has been configured, assign the Context's
         * EntityMap to a class property so it can later be accessed from run(),
         * irrespective of whether this test succeeds or fails. */
        if (isset($this->entityMapObserver)) {
            $this->entityMap = $context->getEntityMap();
        }

        if (isset($createEntities)) {
            $context->createEntities($createEntities);
        }

        assertIsArray($test->operations);
        $this->preventStaleDbVersionError($test->operations, $context);

        $context->startEventObservers();
        $context->startEventCollectors();

        foreach ($test->operations as $o) {
            $operation = new Operation($o, $context);
            $operation->assert();
        }

        $context->stopEventObservers();
        $context->stopEventCollectors();

        if (isset($test->expectEvents)) {
            assertIsArray($test->expectEvents);
            $context->assertExpectedEventsForClients($test->expectEvents);
        }

        if (isset($test->outcome)) {
            assertIsArray($test->outcome);
            $this->assertOutcome($test->outcome);
        }
    }

    /**
     * Checks server version and topology requirements.
     *
     * Arguments for RunOnRequirement::isSatisfied() will be cached internally.
     *
     * @throws SkippedTest unless one or more runOnRequirements are met
     */
    private function checkRunOnRequirements(array $runOnRequirements): void
    {
        static $cachedIsSatisfiedArgs;

        assertNotEmpty($runOnRequirements);
        assertContainsOnly('object', $runOnRequirements);

        if (! isset($cachedIsSatisfiedArgs)) {
            $cachedIsSatisfiedArgs = [
                $this->getServerVersion(),
                $this->getTopology(),
                $this->serverParameterHelper,
                $this->isAuthenticated(),
                $this->isServerless(),
                $this->isClientSideEncryptionSupported(),
            ];
        }

        foreach ($runOnRequirements as $o) {
            $runOnRequirement = new RunOnRequirement($o);
            if ($runOnRequirement->isSatisfied(...$cachedIsSatisfiedArgs)) {
                return;
            }
        }

        // @todo Add server parameter requirements?
        Assert::markTestSkipped(sprintf(
            'Server (version=%s, topology=%s, auth=%s) does not meet test requirements',
            $cachedIsSatisfiedArgs[0],
            $cachedIsSatisfiedArgs[1],
            $cachedIsSatisfiedArgs[3] ? 'yes' : 'no'
        ));
    }

    private function getPrimaryServer(): Server
    {
        $manager = $this->internalClient->getManager();

        return $manager->selectServer(new ReadPreference(ReadPreference::PRIMARY));
    }

    private function getServerVersion(): string
    {
        $database = $this->internalClient->selectDatabase('admin');
        $buildInfo = $database->command(['buildInfo' => 1])->toArray()[0];

        if (isset($buildInfo->version) && is_string($buildInfo->version)) {
            return preg_replace('#^(\d+\.\d+\.\d+).*$#', '\1', $buildInfo->version);
        }

        throw new UnexpectedValueException('Could not determine server version');
    }

    /**
     * Return the topology type.
     *
     * @throws UnexpectedValueException if topology is neither single nor RS nor sharded
     */
    private function getTopology(): string
    {
        switch ($this->getPrimaryServer()->getType()) {
            case Server::TYPE_STANDALONE:
                return RunOnRequirement::TOPOLOGY_SINGLE;

            case Server::TYPE_RS_PRIMARY:
                return RunOnRequirement::TOPOLOGY_REPLICASET;

            case Server::TYPE_MONGOS:
                return $this->isShardedClusterUsingReplicasets()
                    ? RunOnRequirement::TOPOLOGY_SHARDED_REPLICASET
                    : RunOnRequirement::TOPOLOGY_SHARDED;

            case Server::TYPE_LOAD_BALANCER:
                return RunOnRequirement::TOPOLOGY_LOAD_BALANCED;

            default:
                throw new UnexpectedValueException('Topology is neither single nor RS nor sharded');
        }
    }

    /**
     * Return whether the connection is authenticated.
     *
     * Note: if the connectionStatus command is not portable for serverless, it
     * may be necessary to rewrite this to instead inspect the connection string
     * or consult an environment variable, as is done in libmongoc.
     */
    private function isAuthenticated(): bool
    {
        $database = $this->internalClient->selectDatabase('admin');
        $connectionStatus = $database->command(['connectionStatus' => 1])->toArray()[0];

        if (isset($connectionStatus->authInfo->authenticatedUsers) && $connectionStatus->authInfo->authenticatedUsers instanceof BSONArray) {
            return count($connectionStatus->authInfo->authenticatedUsers) > 0;
        }

        throw new UnexpectedValueException('Could not determine authentication status');
    }

    /**
     * Return whether client-side encryption is supported.
     */
    private function isClientSideEncryptionSupported(): bool
    {
        /* CSFLE technically requires FCV 4.2+ but this is sufficient since we
         * do not test on mixed-version clusters. */
        if (version_compare($this->getServerVersion(), '4.2', '<')) {
            return false;
        }

        if (FunctionalTestCase::getModuleInfo('libmongocrypt') === 'disabled') {
            return false;
        }

        return static::isCryptSharedLibAvailable() || static::isMongocryptdAvailable();
    }

    private static function isCryptSharedLibAvailable(): bool
    {
        $cryptSharedLibPath = getenv('CRYPT_SHARED_LIB_PATH');

        if ($cryptSharedLibPath === false) {
            return false;
        }

        return is_readable($cryptSharedLibPath);
    }

    private static function isMongocryptdAvailable(): bool
    {
        $paths = explode(PATH_SEPARATOR, getenv("PATH"));

        foreach ($paths as $path) {
            if (is_executable($path . DIRECTORY_SEPARATOR . 'mongocryptd')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return whether serverless (i.e. proxy as mongos) is being utilized.
     */
    private function isServerless(): bool
    {
        $isServerless = getenv('MONGODB_IS_SERVERLESS');

        return $isServerless !== false ? filter_var($isServerless, FILTER_VALIDATE_BOOLEAN) : false;
    }

    /**
     * Checks is a test format schema version is supported.
     */
    private function isSchemaVersionSupported(string $schemaVersion): bool
    {
        return version_compare($schemaVersion, self::MIN_SCHEMA_VERSION, '>=') && version_compare($schemaVersion, self::MAX_SCHEMA_VERSION, '<=');
    }

    private function isShardedClusterUsingReplicasets(): bool
    {
        $collection = $this->internalClient->selectCollection('config', 'shards');
        $config = $collection->findOne();

        if ($config === null) {
            return false;
        }

        /**
         * Use regular expression to distinguish between standalone or replicaset:
         * Without a replicaset: "host" : "localhost:4100"
         * With a replicaset: "host" : "dec6d8a7-9bc1-4c0e-960c-615f860b956f/localhost:4400,localhost:4401"
         */
        return preg_match('@^.*/.*:\d+@', $config['host']);
    }

    /**
     * Kill all sessions on the cluster.
     *
     * This will clean up any open transactions that may remain from a
     * previously failed test. For sharded clusters, this command will be run
     * on all mongos nodes.
     *
     * This method is a NOP if allowKillAllSessions is false.
     */
    private function killAllSessions(): void
    {
        static $ignoreErrorCodes = [
            self::SERVER_ERROR_INTERRUPTED, // SERVER-38335
            self::SERVER_ERROR_UNAUTHORIZED, // SERVER-54216
        ];

        if (! $this->allowKillAllSessions) {
            return;
        }

        $manager = $this->internalClient->getManager();
        $primary = $manager->selectServer(new ReadPreference(ReadPreference::PRIMARY));
        $servers = $primary->getType() === Server::TYPE_MONGOS ? $manager->getServers() : [$primary];

        foreach ($servers as $server) {
            try {
                /* Skip servers that do not support sessions instead of always
                 * attempting the command and ignoring CommandNotFound(59) */
                if (! isset($server->getInfo()['logicalSessionTimeoutMinutes'])) {
                    continue;
                }

                $command = new DatabaseCommand('admin', ['killAllSessions' => []]);
                $command->execute($server);
            } catch (ServerException $e) {
                if (! in_array($e->getCode(), $ignoreErrorCodes)) {
                    throw $e;
                }
            }
        }
    }

    private function assertOutcome(array $outcome): void
    {
        assertNotEmpty($outcome);
        assertContainsOnly('object', $outcome);

        foreach ($outcome as $data) {
            $collectionData = new CollectionData($data);
            $collectionData->assertOutcome($this->internalClient);
        }
    }

    private function prepareInitialData(array $initialData): void
    {
        assertNotEmpty($initialData);
        assertContainsOnly('object', $initialData);

        foreach ($initialData as $data) {
            $collectionData = new CollectionData($data);
            $collectionData->prepareInitialData($this->internalClient);
        }
    }

    /**
     * Work around potential error executing distinct on sharded clusters.
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst#staledbversion-errors-on-sharded-clusters
     */
    private function preventStaleDbVersionError(array $operations, Context $context): void
    {
        if ($this->getPrimaryServer()->getType() !== Server::TYPE_MONGOS) {
            return;
        }

        $hasStartTransaction = false;
        $hasDistinct = false;
        $collection = null;

        foreach ($operations as $operation) {
            switch ($operation->name) {
                case 'distinct':
                    $hasDistinct = true;
                    $collection = $context->getEntityMap()[$operation->object];
                    break;

                case 'startTransaction':
                    $hasStartTransaction = true;
                    break;

                default:
                    continue 2;
            }

            if ($hasStartTransaction && $hasDistinct) {
                assertInstanceOf(Collection::class, $collection);
                $collection->distinct('foo');

                return;
            }
        }
    }

    private function createContext(): Context
    {
        $context = new Context($this->internalClient, $this->internalClientUri);

        if ($this->getPrimaryServer()->getType() === Server::TYPE_MONGOS) {
            // We assume the internal client URI has multiple mongos hosts
            $multiMongosUri = $this->internalClientUri;

            /* TODO: If an SRV URI is provided, we can consider connecting and
             * checking the topology for multiple mongoses and then selecting a
             * single mongos to reconstruct a single mongos URI; however, that
             * may omit necessary URI options provided by TXT records. */
            assertStringStartsWith('mongodb://', $multiMongosUri);
            assertStringContainsString(',', parse_url($multiMongosUri, PHP_URL_HOST));

            $singleMongosUri = self::removeMultipleHosts($multiMongosUri);

            $context->setUrisForUseMultipleMongoses($singleMongosUri, $multiMongosUri);
        }

        /* TODO: Enable this logic once PHPLIB-794 is implemented. For now, load
         * balancer tests will continue to use MONGODB_URI. */
        if (false && $this->getPrimaryServer()->getType() === Server::TYPE_LOAD_BALANCER && ! $this->isServerless()) {
            $singleMongosUri = getenv('MONGODB_SINGLE_MONGOS_LB_URI');
            $multiMongosUri = getenv('MONGODB_MULTI_MONGOS_LB_URI');

            assertNotFalse($singleMongosUri);
            assertNotFalse($multiMongosUri);

            $context->setUrisForUseMultipleMongoses($singleMongosUri, $multiMongosUri);
        }

        return $context;
    }

    /**
     * Removes any hosts beyond the first in a URI. This function should only be
     * used with a sharded cluster URI, but that is not enforced.
     */
    private static function removeMultipleHosts(string $uri): string
    {
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
}
