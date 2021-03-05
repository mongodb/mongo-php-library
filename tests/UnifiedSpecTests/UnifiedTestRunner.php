<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Collection;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Tests\FunctionalTestCase;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\IncompleteTest;
use PHPUnit\Framework\SkippedTest;
use PHPUnit\Framework\Warning;
use stdClass;
use Throwable;
use UnexpectedValueException;
use function gc_collect_cycles;
use function is_string;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;
use function preg_match;
use function sprintf;
use function version_compare;

/**
 * Unified test runner.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst
 */
final class UnifiedTestRunner
{
    const SERVER_ERROR_INTERRUPTED = 11601;

    const MIN_SCHEMA_VERSION = '1.0';
    const MAX_SCHEMA_VERSION = '1.2';

    /** @var MongoDB\Client */
    private $internalClient;

    /** @var string */
    private $internalClientUri;

    /** @var FailPointObserver */
    private $failPointObserver;

    public function __construct(string $internalClientUri)
    {
        $this->internalClient = FunctionalTestCase::createTestClient($internalClientUri);
        $this->internalClientUri = $internalClientUri;
    }

    public function run(UnifiedTestCase $test)
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
            $this->doTearDown($hasFailed);
        }
    }

    private function doSetUp()
    {
        /* The transactions spec advises calling killAllSessions only at the
         * start of the test suite and after failed tests; however, the "unpin
         * after transient error within a transaction" pinning test causes the
         * subsequent transaction test to block. */
        $this->killAllSessions();

        $this->failPointObserver = new FailPointObserver();
        $this->failPointObserver->start();
    }

    private function doTearDown(bool $hasFailed)
    {
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

    private function doTestCase(stdClass $test, string $schemaVersion, array $runOnRequirements = null, array $createEntities = null, array $initialData = null)
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
     * @throws SkippedTest unless one or more runOnRequirements are met
     */
    private function checkRunOnRequirements(array $runOnRequirements)
    {
        assertNotEmpty($runOnRequirements);
        assertContainsOnly('object', $runOnRequirements);

        $serverVersion = $this->getCachedServerVersion();
        $topology = $this->getCachedTopology();
        $serverParameters = $this->getCachedServerParameters();

        foreach ($runOnRequirements as $o) {
            $runOnRequirement = new RunOnRequirement($o);
            if ($runOnRequirement->isSatisfied($serverVersion, $topology, $serverParameters)) {
                return;
            }
        }

        // @todo Add server parameter requirements?
        Assert::markTestSkipped(sprintf('Server version "%s" and topology "%s" do not meet test requirements', $serverVersion, $topology));
    }

    /**
     * Return the server parameters (cached for subsequent calls).
     */
    private function getCachedServerParameters() : stdClass
    {
        static $cachedServerParameters;

        if (isset($cachedServerParameters)) {
            return $cachedServerParameters;
        }

        $cachedServerParameters = $this->getServerParameters();

        return $cachedServerParameters;
    }

    /**
     * Return the server version (cached for subsequent calls).
     */
    private function getCachedServerVersion() : string
    {
        static $cachedServerVersion;

        if (isset($cachedServerVersion)) {
            return $cachedServerVersion;
        }

        $cachedServerVersion = $this->getServerVersion();

        return $cachedServerVersion;
    }

    /**
     * Return the topology type (cached for subsequent calls).
     *
     * @throws UnexpectedValueException if topology is neither single nor RS nor sharded
     */
    private function getCachedTopology() : string
    {
        static $cachedTopology = null;

        if (isset($cachedTopology)) {
            return $cachedTopology;
        }

        switch ($this->getPrimaryServer()->getType()) {
            case Server::TYPE_STANDALONE:
                $cachedTopology = RunOnRequirement::TOPOLOGY_SINGLE;
                break;

            case Server::TYPE_RS_PRIMARY:
                $cachedTopology = RunOnRequirement::TOPOLOGY_REPLICASET;
                break;

            case Server::TYPE_MONGOS:
                $cachedTopology = $this->isShardedClusterUsingReplicasets()
                    ? RunOnRequirement::TOPOLOGY_SHARDED_REPLICASET
                    : RunOnRequirement::TOPOLOGY_SHARDED;
                break;

            default:
                throw new UnexpectedValueException('Toplogy is neither single nor RS nor sharded');
        }

        return $cachedTopology;
    }

    private function getPrimaryServer() : Server
    {
        $manager = $this->internalClient->getManager();

        return $manager->selectServer(new ReadPreference(ReadPreference::PRIMARY));
    }

    private function getServerParameters() : stdClass
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

    private function getServerVersion() : string
    {
        $database = $this->internalClient->selectDatabase('admin');
        $buildInfo = $database->command(['buildInfo' => 1])->toArray()[0];

        if (isset($buildInfo->version) && is_string($buildInfo->version)) {
            return $buildInfo->version;
        }

        throw new UnexpectedValueException('Could not determine server version');
    }

    /**
     * Checks is a test format schema version is supported.
     */
    private function isSchemaVersionSupported(string $schemaVersion) : bool
    {
        return version_compare($schemaVersion, self::MIN_SCHEMA_VERSION, '>=') && version_compare($schemaVersion, self::MAX_SCHEMA_VERSION, '<');
    }

    private function isShardedClusterUsingReplicasets() : bool
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
     */
    private function killAllSessions()
    {
        $manager = $this->internalClient->getManager();
        $primary = $manager->selectServer(new ReadPreference(ReadPreference::PRIMARY));
        $servers = $primary->getType() === Server::TYPE_MONGOS ? $manager->getServers() : [$primary];

        foreach ($servers as $server) {
            try {
                // Skip servers that do not support sessions
                if (! isset($server->getInfo()['logicalSessionTimeoutMinutes'])) {
                    continue;
                }

                $command = new DatabaseCommand('admin', ['killAllSessions' => []]);
                $command->execute($server);
            } catch (ServerException $e) {
                // Interrupted error is safe to ignore (see: SERVER-38335)
                if ($e->getCode() != self::SERVER_ERROR_INTERRUPTED) {
                    throw $e;
                }
            }
        }
    }

    private function assertOutcome(array $outcome)
    {
        assertNotEmpty($outcome);
        assertContainsOnly('object', $outcome);

        foreach ($outcome as $data) {
            $collectionData = new CollectionData($data);
            $collectionData->assertOutcome($this->internalClient);
        }
    }

    private function prepareInitialData(array $initialData)
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
    private function preventStaleDbVersionError(array $operations, Context $context)
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
