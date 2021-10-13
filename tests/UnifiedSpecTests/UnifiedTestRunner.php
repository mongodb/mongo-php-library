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
use function filter_var;
use function gc_collect_cycles;
use function getenv;
use function in_array;
use function is_string;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;
use function preg_match;
use function preg_replace;
use function sprintf;
use function strpos;
use function version_compare;

use const FILTER_VALIDATE_BOOLEAN;

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
    public const MAX_SCHEMA_VERSION = '1.5';

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
                if (isset($this->entityMapObserver)) {
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

        // Give Context unmodified URI so it can enforce useMultipleMongoses
        $context = new Context($this->internalClient, $this->internalClientUri);

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
                $this->getServerParameters(),
                $this->isAuthenticated(),
                $this->isServerless(),
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
            'Server (version=%s, toplogy=%s, auth=%s) does not meet test requirements',
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

    private function getServerParameters(): stdClass
    {
        $database = $this->internalClient->selectDatabase('admin');
        $cursor = $database->command(
            ['getParameter' => '*'],
            [
                'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
                'typeMap' => [
                    'root' => 'object',
                    'document' => 'object',
                    'array' => 'array',
                ],
            ]
        );

        return $cursor->toArray()[0];
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
                throw new UnexpectedValueException('Toplogy is neither single nor RS nor sharded');
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
     * @see https://github.com/mongodb/specifications/blob/master/source/transactions/tests/README.rst#why-do-tests-that-run-distinct-sometimes-fail-with-staledbversion
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
}
