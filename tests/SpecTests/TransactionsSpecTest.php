<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\BSON\Int64;
use MongoDB\BSON\Timestamp;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\Server;
use stdClass;

use function array_unique;
use function basename;
use function count;
use function dirname;
use function file_get_contents;
use function get_object_vars;
use function glob;

/**
 * Transactions spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/transactions
 */
class TransactionsSpecTest extends FunctionalTestCase
{
    public const INTERRUPTED = 11601;

    /**
     * In addition to the useMultipleMongoses tests, these should all pass
     * before the driver can be considered compatible with MongoDB 4.2.
     */
    private static array $incompleteTests = [
        'transactions/mongos-recovery-token: commitTransaction retry fails on new mongos' => 'isMaster failpoints cannot be disabled',
        'transactions/pin-mongos: remain pinned after non-transient error on commit' => 'Blocked on SPEC-1320',
        'transactions/pin-mongos: unpin after transient error within a transaction and commit' => 'isMaster failpoints cannot be disabled',
    ];

    /**
     * Any tests that rely on session pinning (including targetedFailPoint) must
     * be skipped since libmongoc does not pin on load-balanced toplogies. */
    private static array $incompleteLoadBalancerTests = [
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on insertOne' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient error within a transaction' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on insertOne insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on insertMany insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on updateOne update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on replaceOne update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on updateMany update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on deleteOne delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on deleteMany delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on findOneAndDelete findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on findOneAndUpdate findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on findOneAndReplace findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on bulkWrite insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on bulkWrite update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on bulkWrite delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on find find' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on countDocuments aggregate' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on aggregate aggregate' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on distinct distinct' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: remain pinned after non-transient Interrupted error on runCommand insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on insertOne insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on insertOne insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on insertMany insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on insertMany insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on updateOne update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on updateOne update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on replaceOne update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on replaceOne update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on updateMany update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on updateMany update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on deleteOne delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on deleteOne delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on deleteMany delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on deleteMany delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on findOneAndDelete findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on findOneAndDelete findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on findOneAndUpdate findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on findOneAndUpdate findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on findOneAndReplace findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on findOneAndReplace findAndModify' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on bulkWrite insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on bulkWrite insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on bulkWrite update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on bulkWrite update' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on bulkWrite delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on bulkWrite delete' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on find find' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on find find' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on countDocuments aggregate' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on countDocuments aggregate' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on aggregate aggregate' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on aggregate aggregate' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on distinct distinct' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on distinct distinct' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient connection error on runCommand insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-pin-auto: unpin after transient ShutdownInProgress error on runCommand insert' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-recovery-token: commitTransaction explicit retries include recoveryToken' => 'libmongoc omits recoveryToken for load-balanced topology (CDRIVER-4718)',
        'transactions/mongos-recovery-token: commitTransaction retry succeeds on new mongos' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-recovery-token: commitTransaction retry fails on new mongos' => 'libmongoc does not pin for load-balanced topology',
        'transactions/mongos-recovery-token: abortTransaction sends recoveryToken' => 'libmongoc does not pin for load-balanced topology',
        'transactions/pin-mongos: multiple commits' => 'libmongoc does not pin for load-balanced topology',
        'transactions/pin-mongos: remain pinned after non-transient error on commit' => 'libmongoc does not pin for load-balanced topology',
        'transactions/pin-mongos: unpin after transient error within a transaction' => 'libmongoc does not pin for load-balanced topology',
        'transactions/pin-mongos: unpin after transient error within a transaction and commit' => 'libmongoc does not pin for load-balanced topology',
    ];

    public function setUp(): void
    {
        parent::setUp();

        static::killAllSessions();

        $this->skipIfTransactionsAreNotSupported();
    }

    public function tearDown(): void
    {
        if ($this->hasFailed()) {
            static::killAllSessions();
        }

        parent::tearDown();
    }

    /**
     * Assert that the expected and actual command documents match.
     *
     * Note: this method may modify the $expected object.
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/transactions/tests/README.rst#command-started-events
     * @param stdClass $expected Expected command document
     * @param stdClass $actual   Actual command document
     */
    public static function assertCommandMatches(stdClass $expected, stdClass $actual): void
    {
        if (isset($expected->getMore) && $expected->getMore === 42) {
            static::assertObjectHasAttribute('getMore', $actual);
            static::assertThat($actual->getMore, static::logicalOr(
                static::isInstanceOf(Int64::class),
                static::isType('integer'),
            ));
            unset($expected->getMore);
        }

        if (isset($expected->recoveryToken) && $expected->recoveryToken === 42) {
            static::assertObjectHasAttribute('recoveryToken', $actual);
            static::assertIsObject($actual->recoveryToken);
            unset($expected->recoveryToken);
        }

        if (isset($expected->readConcern->afterClusterTime) && $expected->readConcern->afterClusterTime === 42) {
            static::assertObjectHasAttribute('readConcern', $actual);
            static::assertIsObject($actual->readConcern);
            static::assertObjectHasAttribute('afterClusterTime', $actual->readConcern);
            static::assertInstanceOf(Timestamp::class, $actual->readConcern->afterClusterTime);
            unset($expected->readConcern->afterClusterTime);

            /* If "afterClusterTime" was the only assertion for "readConcern",
             * unset the field to avoid expecting an empty document later. */
            if (get_object_vars($expected->readConcern) === []) {
                unset($expected->readConcern);
            }
        }

        /* TODO: Determine if forcing a new libmongoc client in Context is
         * preferable to skipping the txnNumber assertion. */
        //unset($expected['txnNumber']);

        static::assertCommandOmittedFields($expected, $actual);

        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * @dataProvider provideTransactionsTests
     * @group serverless
     */
    public function testTransactions(stdClass $test, ?array $runOn, array $data, ?string $databaseName = null, ?string $collectionName = null): void
    {
        $this->runTransactionTest($test, $runOn, $data, $databaseName, $collectionName);
    }

    public function provideTransactionsTests(): array
    {
        return $this->provideTests('transactions');
    }

    /** @dataProvider provideTransactionsConvenientApiTests */
    public function testTransactionsConvenientApi(stdClass $test, ?array $runOn, array $data, ?string $databaseName = null, ?string $collectionName = null): void
    {
        $this->runTransactionTest($test, $runOn, $data, $databaseName, $collectionName);
    }

    public function provideTransactionsConvenientApiTests(): array
    {
        return $this->provideTests('transactions-convenient-api');
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @param stdClass $test           Individual "tests[]" document
     * @param array    $runOn          Top-level "runOn" array with server requirements
     * @param array    $data           Top-level "data" array to initialize collection
     * @param string   $databaseName   Name of database under test
     * @param string   $collectionName Name of collection under test
     */
    private function runTransactionTest(stdClass $test, ?array $runOn, array $data, ?string $databaseName = null, ?string $collectionName = null): void
    {
        if (isset(self::$incompleteTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteTests[$this->dataDescription()]);
        }

        if ($this->isLoadBalanced() && isset(self::$incompleteLoadBalancerTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteLoadBalancerTests[$this->dataDescription()]);
        }

        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        if (isset($test->skipReason)) {
            $this->markTestSkipped($test->skipReason);
        }

        $databaseName ??= $this->getDatabaseName();
        $collectionName ??= $this->getCollectionName();

        // Serverless uses a load balancer fronting a single proxy (PHPLIB-757)
        $useMultipleMongoses = $this->isMongos() || ($this->isLoadBalanced() && ! $this->isServerless())
            ? ($test->useMultipleMongoses ?? false)
            : false;

        $context = Context::fromTransactions($test, $databaseName, $collectionName, $useMultipleMongoses);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->createTestCollection();
        $this->insertDataFixtures($data);
        $this->preventStaleDbVersionError($test->operations);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromTransactions($context->getClient(), $test->expectations);
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

    private function provideTests(string $dir): array
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/' . $dir . '/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename(dirname($filename)) . '/' . basename($filename, '.json');
            $runOn = $json->runOn ?? null;
            $data = $json->data ?? [];
            // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;
            // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }

    /**
     * Prose test 1: Test that starting a new transaction on a pinned
     * ClientSession unpins the session and normal server selection is performed
     * for the next operation.
     */
    public function testStartingNewTransactionOnPinnedSessionUnpinsSession(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        if (! $this->isMongos()) {
            $this->markTestSkipped('Pinning tests require mongos');
        }

        $client = self::createTestClient(static::getUri(true));

        $session = $client->startSession();
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        // Create collection before transaction
        $collection->insertOne([]);

        $session->startTransaction([]);
        $collection->insertOne([], ['session' => $session]);
        $session->commitTransaction();

        $servers = [];
        for ($i = 0; $i < 50; $i++) {
            $session->startTransaction([]);
            $cursor = $collection->find([], ['session' => $session]);
            $servers[] = $cursor->getServer()->getHost() . ':' . $cursor->getServer()->getPort();
            $this->assertInstanceOf(Server::class, $session->getServer());
            $session->commitTransaction();
        }

        $servers = array_unique($servers);
        $this->assertGreaterThan(1, count($servers));

        $session->endSession();
    }

    /**
     * Prose test 2: Test non-transaction operations using a pinned
     * ClientSession unpins the session and normal server selection is
     * performed.
     */
    public function testRunningNonTransactionOperationOnPinnedSessionUnpinsSession(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        if (! $this->isMongos()) {
            $this->markTestSkipped('Pinning tests require mongos');
        }

        $client = self::createTestClient(static::getUri(true));

        $session = $client->startSession();
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        // Create collection before transaction
        $collection->insertOne([]);

        $session->startTransaction([]);
        $collection->insertOne([], ['session' => $session]);
        $session->commitTransaction();

        $servers = [];
        for ($i = 0; $i < 50; $i++) {
            $cursor = $collection->find([], ['session' => $session]);
            $servers[] = $cursor->getServer()->getHost() . ':' . $cursor->getServer()->getPort();
            $this->assertNull($session->getServer());
        }

        $servers = array_unique($servers);
        $this->assertGreaterThan(1, count($servers));

        $session->endSession();
    }

    /**
     * Create the collection, since it cannot be created within a transaction.
     */
    protected function createTestCollection(): void
    {
        $context = $this->getContext();

        $database = $context->getDatabase();
        $database->createCollection($context->collectionName, $context->defaultWriteOptions);
    }

    /**
     * Kill all sessions on the cluster.
     *
     * This will clean up any open transactions that may remain from a
     * previously failed test. For sharded clusters, this command will be run
     * on all mongos nodes.
     */
    private static function killAllSessions(): void
    {
        // killAllSessions is not supported on serverless, see CLOUDP-84298
        if (static::isServerless()) {
            return;
        }

        $manager = static::createTestManager();
        $primary = $manager->selectServer();

        $servers = $primary->getType() === Server::TYPE_MONGOS
            ? $manager->getServers()
            : [$primary];

        foreach ($servers as $server) {
            try {
                // Skip servers that do not support sessions
                if (! isset($server->getInfo()['logicalSessionTimeoutMinutes'])) {
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
     * Work around potential error executing distinct on sharded clusters.
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/transactions/tests#why-do-tests-that-run-distinct-sometimes-fail-with-staledbversion
     */
    private function preventStaleDbVersionError(array $operations): void
    {
        if (! $this->isShardedCluster()) {
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
