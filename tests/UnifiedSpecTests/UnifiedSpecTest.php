<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use Exception;
use Generator;
use MongoDB\Tests\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\SkippedTest;
use PHPUnit\Framework\Warning;

use function array_flip;
use function glob;
use function str_starts_with;

/**
 * Unified test format spec tests.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst
 */
class UnifiedSpecTest extends FunctionalTestCase
{
    /**
     * Incomplete test groups are listed here. Any data set that starts with a
     * string listed in this index will be skipped with the message given as
     * value.
     *
     * @var array<string, string>
     */
    private static array $incompleteTestGroups = [
        // Many load balancer tests use CMAP events and/or assertNumberConnectionsCheckedOut
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled' => 'PHPC does not implement CMAP',
        'load-balancers/wait queue timeout errors include details about checked out connections' => 'PHPC does not implement CMAP',
        // mongoc_cluster_stream_for_server does not retry handshakes (CDRIVER-4532, PHPLIB-1033, PHPLIB-1042)
        'retryable-reads/retryable reads handshake failures' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures' => 'Handshakes are not retried (CDRIVER-4532)',
        // sort option for update operations is not supported (PHPLIB-1492)
        'crud/BulkWrite replaceOne-sort' => 'Sort for replace operations is not supported (PHPLIB-1492)',
        'crud/BulkWrite updateOne-sort' => 'Sort for update operations is not supported (PHPLIB-1492)',
        'crud/replaceOne-sort' => 'Sort for replace operations is not supported (PHPLIB-1492)',
        'crud/updateOne-sort' => 'Sort for update operations is not supported (PHPLIB-1492)',
    ];

    /** @var array<string, string> */
    private static array $incompleteTests = [
        // Many load balancer tests use CMAP events and/or assertNumberConnectionsCheckedOut
        'load-balancers/monitoring events include correct fields: poolClearedEvent events include serviceId' => 'PHPC does not implement CMAP',
        // Skips dating back to legacy transaction tests
        'transactions/mongos-recovery-token: commitTransaction retry fails on new mongos' => 'isMaster failpoints cannot be disabled',
        'transactions/pin-mongos: remain pinned after non-transient error on commit' => 'Blocked on DRIVERS-2104',
        'transactions/pin-mongos: unpin after transient error within a transaction and commit' => 'isMaster failpoints cannot be disabled',
        // PHPC does not implement CMAP
        'valid-pass/assertNumberConnectionsCheckedOut: basic assertion succeeds' => 'PHPC does not implement CMAP',
        'valid-pass/entity-client-cmap-events: events are captured during an operation' => 'PHPC does not implement CMAP',
        'valid-pass/expectedEventsForClient-eventType: eventType can be set to command and cmap' => 'PHPC does not implement CMAP',
        'valid-pass/expectedEventsForClient-eventType: eventType defaults to command if unset' => 'PHPC does not implement CMAP',
        // CSOT is not yet implemented (PHPC-1760)
        'valid-pass/collectionData-createOptions: collection is created with the correct options' => 'CSOT is not yet implemented (PHPC-1760)',
        'valid-pass/matches-lte-operator: special lte matching operator' => 'CSOT is not yet implemented (PHPC-1760)',
        // libmongoc always adds readConcern to aggregate command
        'index-management/search index operations ignore read and write concern: listSearchIndexes ignores read and write concern' => 'libmongoc appends readConcern to aggregate command',
        // Uses an invalid object name
        'run-command/runCursorCommand: does not close the cursor when receiving an empty batch' => 'Uses an invalid object name',
        // GridFS deprecated fields are removed
        'gridfs/gridfs-upload-disableMD5: upload when length is 0 sans MD5' => 'Deprecated fields are removed',
        'gridfs/gridfs-upload-disableMD5: upload when length is 1 sans MD5' => 'Deprecated fields are removed',
        'gridfs/gridfs-upload: upload when contentType is provided' => 'Deprecated fields are removed',
    ];

    /**
     * Any tests with duplicate names are skipped here. While test names should
     * not be reused in spec tests, this offers a way to skip such tests until
     * the name is changed.
     *
     * @var array<string, string>
     */
    private static array $duplicateTests = [];

    /**
     * Any tests that rely on session pinning (including targetedFailPoint) must
     * be skipped since libmongoc does not pin on load-balanced toplogies.
     *
     * @var array<string, string>
     */
    private static array $incompleteLoadBalancerTests = [
        'transactions/mongos-recovery-token: commitTransaction explicit retries include recoveryToken' => 'libmongoc omits recoveryToken for load-balanced topology (CDRIVER-4718)',
        'transactions/pin-mongos: multiple commits' => 'libmongoc does not pin for load-balanced topology',
    ];

    private static UnifiedTestRunner $runner;

    private static string $testDir = __DIR__ . '/../specifications/source';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        /* Provide unmodified URI for internal client, since it may need to
         * execute commands on multiple mongoses (e.g. killAllSessions) */
        self::$runner = new UnifiedTestRunner(static::getUri(true));
    }

    public function setUp(): void
    {
        parent::setUp();

        if (isset(self::$incompleteTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteTests[$this->dataDescription()]);
        }

        if ($this->isLoadBalanced() && isset(self::$incompleteLoadBalancerTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteLoadBalancerTests[$this->dataDescription()]);
        }

        foreach (self::$incompleteTestGroups as $testGroup => $reason) {
            if (str_starts_with($this->dataDescription(), $testGroup)) {
                $this->markTestIncomplete($reason);
            }
        }
    }

    #[DataProvider('provideAtlasDataLakeTests')]
    #[Group('atlas-data-lake')]
    public function testAtlasDataLake(UnifiedTestCase $test): void
    {
        if (! $this->isAtlasDataLake()) {
            $this->markTestSkipped('Server is not Atlas Data Lake');
        }

        self::$runner->run($test);
    }

    public static function provideAtlasDataLakeTests(): Generator
    {
        return self::provideTests('atlas-data-lake-testing/tests/unified', 'atlas-data-lake');
    }

    #[DataProvider('provideChangeStreamsTests')]
    #[Group('serverless')]
    public function testChangeStreams(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideChangeStreamsTests(): Generator
    {
        return self::provideTests('change-streams/tests/unified', 'change-streams');
    }

    #[DataProvider('provideClientSideEncryptionTests')]
    #[Group('csfle')]
    #[Group('serverless')]
    public function testClientSideEncryption(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideClientSideEncryptionTests(): Generator
    {
        return self::provideTests('client-side-encryption/tests/unified', 'client-side-encryption');
    }

    #[DataProvider('provideCollectionManagementTests')]
    #[Group('serverless')]
    public function testCollectionManagement(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideCollectionManagementTests(): Generator
    {
        return self::provideTests('collection-management/tests', 'collection-management');
    }

    #[DataProvider('provideCommandMonitoringTests')]
    #[Group('serverless')]
    public function testCommandMonitoring(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideCommandMonitoringTests(): Generator
    {
        return self::provideTests('command-logging-and-monitoring/tests/monitoring', 'command-monitoring');
    }

    #[DataProvider('provideCrudTests')]
    #[Group('serverless')]
    public function testCrud(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideCrudTests(): Generator
    {
        return self::provideTests('crud/tests/unified', 'crud');
    }

    #[DataProvider('provideGridFSTests')]
    #[Group('serverless')]
    public function testGridFS(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideGridFSTests(): Generator
    {
        return self::provideTests('gridfs/tests', 'gridfs');
    }

    #[DataProvider('provideLoadBalancers')]
    #[Group('serverless')]
    public function testLoadBalancers(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideLoadBalancers(): Generator
    {
        return self::provideTests('load-balancers/tests', 'load-balancers');
    }

    #[DataProvider('provideReadWriteConcernTests')]
    public function testReadWriteConcern(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideReadWriteConcernTests(): Generator
    {
        return self::provideTests('read-write-concern/tests/operation', 'read-write-concern');
    }

    #[DataProvider('provideRetryableReadsTests')]
    #[Group('serverless')]
    public function testRetryableReads(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideRetryableReadsTests(): Generator
    {
        return self::provideTests('retryable-reads/tests/unified', 'retryable-reads');
    }

    #[DataProvider('provideRetryableWritesTests')]
    #[Group('serverless')]
    public function testRetryableWrites(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideRetryableWritesTests(): Generator
    {
        return self::provideTests('retryable-writes/tests/unified', 'retryable-writes');
    }

    #[DataProvider('provideRunCommandTests')]
    #[Group('serverless')]
    public function testRunCommand(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideRunCommandTests(): Generator
    {
        return self::provideTests('run-command/tests/unified', 'run-command');
    }

    #[DataProvider('provideSessionsTests')]
    #[Group('serverless')]
    public function testSessions(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideSessionsTests(): Generator
    {
        return self::provideTests('sessions/tests', 'sessions');
    }

    #[DataProvider('provideTransactionsTests')]
    #[Group('serverless')]
    public function testTransactions(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideTransactionsTests(): Generator
    {
        return self::provideTests('transactions/tests/unified', 'transactions');
    }

    #[DataProvider('provideTransactionsConvenientApiTests')]
    public function testTransactionsConvenientApi(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideTransactionsConvenientApiTests(): Generator
    {
        return self::provideTests('transactions-convenient-api/tests/unified', 'transactions-convenient-api');
    }

    #[DataProvider('provideVersionedApiTests')]
    #[Group('serverless')]
    #[Group('versioned-api')]
    public function testVersionedApi(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideVersionedApiTests(): Generator
    {
        return self::provideTests('versioned-api/tests', 'versioned-api');
    }

    #[DataProvider('providePassingTests')]
    public function testPassingTests(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function providePassingTests(): Generator
    {
        yield from self::provideTests('unified-test-format/tests/valid-pass', 'valid-pass');
    }

    #[DataProvider('provideFailingTests')]
    public function testFailingTests(UnifiedTestCase $test): void
    {
        // Cannot use expectException(), as it ignores PHPUnit Exceptions
        $failed = false;

        // phpcs:disable SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
        /* Failing tests should never produce PHP errors, so intentionally catch
         * Exception instead of Throwable. */
        try {
            self::$runner->run($test);
        } catch (Exception $e) {
            // Respect skipped tests (e.g. evaluated runOnRequirements)
            if ($e instanceof SkippedTest) {
                throw $e;
            }

            /* As is done in PHPUnit\Framework\TestCase::runBare(), exceptions
             * other than a select few will indicate a test failure. We cannot
             * call TestCase::hasFailed() because runBare() has yet to catch the
             * exceptions and update the TestCase's status.
             *
             * IncompleteTest is intentionally omitted as it is thrown for an
             * incompatible schema. This differs from PHPUnit's internal logic.
             */
            $failed = ! ($e instanceof Warning);
        }

        // phpcs:enable

        $this->assertTrue($failed, 'Expected test to throw an exception');
    }

    public static function provideFailingTests(): Generator
    {
        yield from self::provideTests('unified-test-format/tests/valid-fail', 'valid-fail');
    }

    #[DataProvider('provideIndexManagementTests')]
    public function testIndexManagement(UnifiedTestCase $test): void
    {
        if (self::isAtlas()) {
            self::markTestSkipped('Search Indexes tests must run on a non-Atlas cluster');
        }

        if (! self::isEnterprise()) {
            self::markTestSkipped('Specific Atlas error messages are only available on Enterprise server');
        }

        self::$runner->run($test);
    }

    public static function provideIndexManagementTests(): Generator
    {
        yield from self::provideTests('index-management/tests', 'index-management');
    }

    private static function provideTests(string $directory, string $testGroup): Generator
    {
        $pattern = self::$testDir . '/' . $directory . '/*.json';

        $duplicateTests = array_flip(self::$duplicateTests);

        foreach (glob($pattern) as $filename) {
            foreach (UnifiedTestCase::fromFile($filename) as $name => $test) {
                $testKey = $testGroup . '/' . $name;

                if (isset($duplicateTests[$testKey])) {
                    continue;
                }

                yield $testKey => [$test];
            }
        }
    }
}
