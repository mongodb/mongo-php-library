<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\BSON\Int64;
use MongoDB\BSON\Timestamp;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\ServerException;
use stdClass;

/**
 * Transactions spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/transactions
 */
class TransactionsSpecTest extends FunctionalTestCase
{
    const INTERRUPTED = 11601;

    /* In addition to the useMultipleMongoses tests, these should all pass
     * before the driver can be considered compatible with MongoDB 4.2. */
    private static $incompleteTests = [
        'error-labels: add unknown commit label to MaxTimeMSExpired' => 'PHPC-1382',
        'error-labels: add unknown commit label to writeConcernError MaxTimeMSExpired' => 'PHPC-1382',
        'read-concern: only first countDocuments includes readConcern' => 'PHPLIB-417',
        'read-concern: countDocuments ignores collection readConcern' => 'PHPLIB-417',
        'read-pref: default readPreference' => 'PHPLIB does not properly inherit readPreference for transactions',
        'read-pref: primary readPreference' => 'PHPLIB does not properly inherit readPreference for transactions',
        'run-command: run command with secondary read preference in client option and primary read preference in transaction options' => 'PHPLIB does not properly inherit readPreference for transactions',
        'transaction-options: transaction options inherited from defaultTransactionOptions' => 'PHPC-1382',
        'transaction-options: startTransaction options override defaults' => 'PHPC-1382',
        'transaction-options: defaultTransactionOptions override client options' => 'PHPC-1382',
        'transaction-options: transaction options inherited from client' => 'PHPLIB does not properly inherit readConcern for transactions',
        'transaction-options: readConcern local in defaultTransactionOptions' => 'PHPLIB does not properly inherit readConcern for transactions',
        'transaction-options: readConcern snapshot in startTransaction options' => 'PHPLIB does not properly inherit readConcern for transactions',
    ];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::killAllSessions();
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            static::killAllSessions();
        }

        parent::tearDown();
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
        $manager = new Manager(static::getUri());
        $primary = $manager->selectServer(new ReadPreference('primary'));

        $servers = ($primary->getType() === Server::TYPE_MONGOS)
            ? $manager->getServers()
            : [$primary];

        foreach ($servers as $server) {
            try {
                // Skip servers that do not support sessions
                if (!isset($server->getInfo()['logicalSessionTimeoutMinutes'])) {
                    continue;
                }
                $server->executeCommand('admin', new Command(['killAllSessions' => []]));
            } catch (ServerException $e) {
                // Interrupted error is safe to ignore (see: SERVER-38335)
                if ($e->getCode() != self::INTERRUPTED) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Assert that the expected and actual command documents match.
     *
     * Note: this method may modify the $expectedCommand object.
     *
     * @param stdClass $expectedCommand Expected command document
     * @param stdClass $actualCommand   Actual command document
     */
    public function assertSameCommand(stdClass $expectedCommand, stdClass $actualCommand)
    {
        if (isset($expectedCommand->getMore) && $expectedCommand->getMore === 42) {
            $this->assertObjectHasAttribute('getMore', $actualCommand);
            $this->assertThat($actualCommand->getMore, $this->logicalOr(
                $this->isInstanceOf(Int64::class),
                $this->isType('integer')
            ));
            unset($expectedCommand->getMore);
        }

        if (isset($expectedCommand->recoveryToken) && $expectedCommand->recoveryToken === 42) {
            $this->assertObjectHasAttribute('recoveryToken', $actualCommand);
            $this->assertInternalType('object', $actualCommand->recoveryToken);
            unset($expectedCommand->recoveryToken);
        }

        if (isset($expectedCommand->readConcern->afterClusterTime) && $expectedCommand->readConcern->afterClusterTime === 42) {
            $this->assertObjectHasAttribute('readConcern', $actualCommand);
            $this->assertInternalType('object', $actualCommand->readConcern);
            $this->assertObjectHasAttribute('afterClusterTime', $actualCommand->readConcern);
            $this->assertInstanceOf(Timestamp::class, $actualCommand->readConcern->afterClusterTime);
            unset($expectedCommand->readConcern->afterClusterTime);

            /* If "afterClusterTime" was the only assertion for "readConcern",
             * unset the field to avoid expecting an empty document later. */
            if (get_object_vars($expectedCommand->readConcern) === []) {
                unset($expectedCommand->readConcern);
            }
        }

        /* TODO: Determine if forcing a new libmongoc client in Context is
         * preferable to skipping the txnNumber assertion. */
        //unset($expectedCommand['txnNumber']);

        foreach ($expectedCommand as $key => $value) {
            if ($value === null) {
                $this->assertObjectNotHasAttribute($key, $actualCommand);
                unset($expectedCommand->{$key});
            }
        }

        $this->assertMatchesDocument($expectedCommand, $actualCommand);
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param string    $name           Test name
     * @param stdClass  $test           Individual "tests[]" document
     * @param array     $runOn          Top-level "runOn" array with server requirements
     * @param array     $data           Top-level "data" array to initialize collection
     * @param string    $databaseName   Name of database under test
     * @param string    $collectionName Name of collection under test
     */
    public function testTransactions($name, stdClass $test, array $runOn = null, array $data, $databaseName = null, $collectionName = null)
    {
        $this->setName($name);

        if (isset(self::$incompleteTests[$name])) {
            $this->markTestIncomplete(self::$incompleteTests[$name]);
        }

        // TODO: Revise this once a test environment with multiple mongos nodes is available (see: PHPLIB-430)
        if (isset($test->useMultipleMongoses) && $test->useMultipleMongoses && $this->isShardedCluster()) {
            $this->markTestIncomplete('"useMultipleMongoses" is not supported');
        }

        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        if (isset($test->skipReason)) {
            $this->markTestSkipped($test->skipReason);
        }

        $databaseName = isset($databaseName) ? $databaseName : $this->getDatabaseName();
        $collectionName = isset($collectionName) ? $collectionName : $this->getCollectionName();

        $context = Context::fromTransactions($test, $databaseName, $collectionName);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->createTestCollection();
        $this->insertDataFixtures($data);
        $this->preventStaleDbVersionError($test->operations);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromTransactions($test->expectations);
            $commandExpectations->startMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromTransactions($operation)->assert($this, $context);
        }

        $context->session0->endSession();
        $context->session1->endSession();

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
            $commandExpectations->assert($this, $context);
        }

        if (isset($test->outcome->collection->data)) {
            $this->assertOutcomeCollectionData($test->outcome->collection->data);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/transactions/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename($filename, '.json');
            $runOn = isset($json->runOn) ? $json->runOn : null;
            $data = isset($json->data) ? $json->data : [];
            $databaseName = isset($json->database_name) ? $json->database_name : null;
            $collectionName = isset($json->collection_name) ? $json->collection_name : null;

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[] = [$name, $test, $runOn, $data, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }

    /**
     * Create the collection, since it cannot be created within a transaction.
     */
    protected function createTestCollection()
    {
        $context = $this->getContext();

        $database = $context->getDatabase();
        $database->createCollection($context->collectionName, $context->defaultWriteOptions);
    }

    /**
     * Work around potential error executing distinct on sharded clusters.
     *
     * @param array $operations
     * @see https://github.com/mongodb/specifications/tree/master/source/transactions/tests#why-do-tests-that-run-distinct-sometimes-fail-with-staledbversionts.
     */
    private function preventStaleDbVersionError(array $operations)
    {
        if (!$this->isShardedCluster()) {
            return;
        }

        foreach ($operations as $operation) {
            if ($operation->name === 'distinct') {
                $this->getContext()->getCollection()->distinct('foo');
                return;
            }
        }
    }
}
