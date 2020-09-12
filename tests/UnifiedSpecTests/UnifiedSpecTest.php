<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Client;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Tests\FunctionalTestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use stdClass;
use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function file_get_contents;
use function glob;
use function in_array;
use function json_encode;
use function sprintf;
use function version_compare;

/**
 * Unified spec test runner.
 *
 * @see https://github.com/mongodb/specifications/pull/846
 */
class UnifiedSpecTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    const SERVER_ERROR_INTERRUPTED = 11601;

    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';
    const TOPOLOGY_SHARDED_REPLICASET = 'sharded-replicaset';

    /** @var MongoDB\Client */
    private static $internalClient;

    private static function doSetUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$internalClient = new Client(static::getUri());
        self::killAllSessions();
    }

    private function doSetUp()
    {
        parent::setUp();
    }

    private function doTearDown()
    {
        if ($this->hasFailed()) {
            self::killAllSessions();
        }

        parent::tearDown();
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass $test              Individual object in "tests[]"
     * @param string   $schemaVersion     Top-level "schemaVersion"
     * @param array    $runOnRequirements Top-level "runOnRequirements"
     * @param array    $createEntities    Top-level "createEntities"
     * @param array    $initialData       Top-level "initialData"
     */
    public function testCase(stdClass $test, string $schemaVersion, array $runOnRequirements = null, array $createEntities = null, array $initialData = null)
    {
        if (! $this->isSchemaVersionSupported($schemaVersion)) {
            $this->markTestIncomplete(sprintf('Test format schema version "%s" is not supported', $schemaVersion));
        }

        if (isset($runOnRequirements)) {
            $this->checkRunOnRequirements($runOnRequirements);
        }

        if (isset($test->skipReason)) {
            $this->markTestSkipped($test->skipReason);
        }

        if (isset($test->runOnRequirements)) {
            $this->checkRunOnRequirements($runOnRequirements);
        }

        if (isset($initialData)) {
            $this->prepareInitialData($initialData);
        }

        $context = new Context(static::getUri());

        if (isset($createEntities)) {
            $context->createEntities($createEntities);
        }

        
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/*.json') as $filename) {
            /* Decode the file through the driver's extended JSON parser to
             * ensure proper handling of special types. */
            $json = toPHP(fromJSON(file_get_contents($filename)));

            $description = $json->description;
            $schemaVersion = $json->schemaVersion;
            $runOnRequirements = $json->runOnRequirements ?? null;
            $createEntities = $json->createEntities ?? null;
            $initialData = $json->initialData ?? null;

            foreach ($json->tests as $test) {
                $name = $description . ': ' . $test->description;
                $testArgs[$name] = [$test, $schemaVersion, $runOnRequirements, $createEntities, $initialData];
            }
        }

        return $testArgs;
    }

    /**
     * Checks server version and topology requirements.
     *
     * @param array $runOnRequirements
     * @throws SkippedTest unless one or more runOnRequirements are met
     */
    private function checkRunOnRequirements(array $runOnRequirements)
    {
        $this->assertNotEmpty($runOnRequirements);

        $serverVersion = $this->getCachedServerVersion();
        $topology = $this->getCachedTopology();

        foreach ($runOnRequirements as $o) {
            $runOnRequirement = new RunOnRequirement($o);
            if ($runOnRequirement->isSatisfied($serverVersion, $topology)) {
                return;
            }
        }

        $this->markTestSkipped(sprintf('Server version "%s" and topology "%s" do not meet test requirements', $serverVersion, $topology));
    }

    /**
     * Return the server version (cached for subsequent calls).
     *
     * @return string
     */
    private function getCachedServerVersion()
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
     * @return string
     * @throws UnexpectedValueException if topology is neither single nor RS nor sharded
     */
    private function getCachedTopology()
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

    /**
     * Checks is a test format schema version is supported.
     *
     * @param string $schemaVersion
     * @return boolean
     */
    private function isSchemaVersionSupported($schemaVersion)
    {
        if (version_compare($schemaVersion, '1.0', '>=') && version_compare($schemaVersion, '1.1', '<')) {
            return true;
        }

        return false;
    }

    /**
     * Kill all sessions on the cluster.
     *
     * This will clean up any open transactions that may remain from a
     * previously failed test. For sharded clusters, this command will be run
     * on all mongos nodes.
     */
    private static function killAllSessions()
    {
        $manager = self::$internalClient->getManager();
        $primary = $manager->selectServer(new ReadPreference(ReadPreference::PRIMARY));
        $servers = $primary->getType() === Server::TYPE_MONGOS ? $manager->getServers() : [$primary];

        foreach ($servers as $server) {
            try {
                // Skip servers that do not support sessions
                if (! isset($server->getInfo()['logicalSessionTimeoutMinutes'])) {
                    continue;
                }
                $server->executeCommand('admin', new Command(['killAllSessions' => []]));
            } catch (ServerException $e) {
                // Interrupted error is safe to ignore (see: SERVER-38335)
                if ($e->getCode() != self::SERVER_ERROR_INTERRUPTED) {
                    throw $e;
                }
            }
        }
    }

    private function prepareInitialData(array $initialData)
    {
        $this->assertNotEmpty($initialData);

        foreach ($initialData as $data) {
            $collectionData = new CollectionData($data);
            $collectionData->prepare(self::$internalClient);
        }
    }
}
