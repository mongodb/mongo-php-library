<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use Exception;
use Generator;
use MongoDB\Tests\FunctionalTestCase;
use PHPUnit\Framework\SkippedTest;
use PHPUnit\Framework\Warning;

use function basename;
use function dirname;
use function glob;

/**
 * Unified test format spec tests.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst
 * @group serverless
 */
class UnifiedSpecTest extends FunctionalTestCase
{
    /** @var array */
    private static $incompleteTests = [
        // Many load balancer tests use CMAP events and/or assertNumberConnectionsCheckedOut
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: no connection is pinned if all documents are returned in the initial batch' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: pinned connections are returned when the cursor is drained' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: pinned connections are returned to the pool when the cursor is closed' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: pinned connections are not returned after an network error during getMore' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: pinned connections are returned after a network error during a killCursors request' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: pinned connections are not returned to the pool after a non-network error on getMore' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: aggregate pins the cursor to a connection' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: listCollections pins the cursor to a connection' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: listIndexes pins the cursor to a connection' => 'PHPC does not implement CMAP',
        'load-balancers/cursors are correctly pinned to connections for load-balanced clusters: change streams pin to a connection' => 'PHPC does not implement CMAP',
        'load-balancers/monitoring events include correct fields: poolClearedEvent events include serviceId' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled: only connections for a specific serviceId are closed when pools are cleared' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled: errors during the initial connection hello are ignored' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled: errors during authentication are processed' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled: stale errors are ignored' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: all operations go to the same mongos' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: transaction can be committed multiple times' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is not released after a non-transient CRUD error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is not released after a non-transient commit error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a non-transient abort error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a transient non-network CRUD error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a transient network CRUD error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a transient non-network commit error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a transient network commit error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a transient non-network abort error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released after a transient network abort error' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is released on successful abort' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is returned when a new transaction is started' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: pinned connection is returned when a non-transaction operation uses the session' => 'PHPC does not implement CMAP',
        'load-balancers/transactions are correctly pinned to connections for load-balanced clusters: a connection can be shared by a transaction and a cursor' => 'PHPC does not implement CMAP',
        'load-balancers/wait queue timeout errors include details about checked out connections: wait queue timeout errors include cursor statistics' => 'PHPC does not implement CMAP',
        'load-balancers/wait queue timeout errors include details about checked out connections: wait queue timeout errors include transaction statistics' => 'PHPC does not implement CMAP',
        // PHPC does not implement CMAP
        'valid-pass/assertNumberConnectionsCheckedOut: basic assertion succeeds' => 'PHPC does not implement CMAP',
        'valid-pass/entity-client-cmap-events: events are captured during an operation' => 'PHPC does not implement CMAP',
        'valid-pass/expectedEventsForClient-eventType: eventType can be set to command and cmap' => 'PHPC does not implement CMAP',
        'valid-pass/expectedEventsForClient-eventType: eventType defaults to command if unset' => 'PHPC does not implement CMAP',
        // CSOT is not yet implemented
        'valid-pass/collectionData-createOptions: collection is created with the correct options' => 'CSOT is not yet implemented (PHPC-1760)',
        'valid-pass/createEntities-operation: createEntities operation' => 'CSOT is not yet implemented (PHPC-1760)',
        'valid-pass/entity-cursor-iterateOnce: iterateOnce' => 'CSOT is not yet implemented (PHPC-1760)',
        'valid-pass/matches-lte-operator: special lte matching operator' => 'CSOT is not yet implemented (PHPC-1760)',
        // BulkWriteException cannot access server response
        'crud/bulkWrite-errorResponse: bulkWrite operations support errorResponse assertions' => 'BulkWriteException cannot access server response',
        'crud/deleteOne-errorResponse: delete operations support errorResponse assertions' => 'BulkWriteException cannot access server response',
        'crud/insertOne-errorResponse: insert operations support errorResponse assertions' => 'BulkWriteException cannot access server response',
        'crud/updateOne-errorResponse: update operations support errorResponse assertions' => 'BulkWriteException cannot access server response',
    ];

    /** @var UnifiedTestRunner */
    private static $runner;

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
    }

    /**
     * @dataProvider provideChangeStreamsTests
     */
    public function testChangeStreams(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideChangeStreamsTests()
    {
        return $this->provideTests(__DIR__ . '/change-streams/*.json');
    }

    /**
     * @dataProvider provideClientSideEncryptionTests
     */
    public function testClientSideEncryption(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideClientSideEncryptionTests()
    {
        return $this->provideTests(__DIR__ . '/client-side-encryption/*.json');
    }

    /**
     * @dataProvider provideCollectionManagementTests
     */
    public function testCollectionManagement(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideCollectionManagementTests()
    {
        return $this->provideTests(__DIR__ . '/collection-management/*.json');
    }

    /**
     * @dataProvider provideCommandMonitoringTests
     */
    public function testCommandMonitoring(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideCommandMonitoringTests()
    {
        return $this->provideTests(__DIR__ . '/command-monitoring/*.json');
    }

    /**
     * @dataProvider provideCrudTests
     */
    public function testCrud(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideCrudTests()
    {
        return $this->provideTests(__DIR__ . '/crud/*.json');
    }

    /**
     * @dataProvider provideGridFSTests
     */
    public function testGridFS(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideGridFSTests()
    {
        return $this->provideTests(__DIR__ . '/gridfs/*.json');
    }

    /**
     * @dataProvider provideLoadBalancers
     */
    public function testLoadBalancers(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideLoadBalancers()
    {
        return $this->provideTests(__DIR__ . '/load-balancers/*.json');
    }

    /**
     * @dataProvider provideSessionsTests
     */
    public function testSessions(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideSessionsTests()
    {
        return $this->provideTests(__DIR__ . '/sessions/*.json');
    }

    /**
     * @dataProvider provideTransactionsTests
     */
    public function testTransactions(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideTransactionsTests()
    {
        return $this->provideTests(__DIR__ . '/transactions/*.json');
    }

    /**
     * @dataProvider provideVersionedApiTests
     * @group versioned-api
     */
    public function testVersionedApi(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function provideVersionedApiTests()
    {
        return $this->provideTests(__DIR__ . '/versioned-api/*.json');
    }

    /**
     * @dataProvider providePassingTests
     */
    public function testPassingTests(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public function providePassingTests()
    {
        yield from $this->provideTests(__DIR__ . '/valid-pass/*.json');
    }

    /**
     * @dataProvider provideFailingTests
     */
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

    public function provideFailingTests()
    {
        yield from $this->provideTests(__DIR__ . '/valid-fail/*.json');
    }

    private function provideTests(string $pattern): Generator
    {
        foreach (glob($pattern) as $filename) {
            $group = basename(dirname($filename));

            foreach (UnifiedTestCase::fromFile($filename) as $name => $test) {
                yield $group . '/' . $name => [$test];
            }
        }
    }
}
