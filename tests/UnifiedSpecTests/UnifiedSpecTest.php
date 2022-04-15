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
        'load-balancers/load-balancers/state change errors are correctly handled: only connections for a specific serviceId are closed when pools are cleared' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled: only connections for a specific serviceId are closed when pools are cleared' => 'PHPC does not implement CMAP',
        'load-balancers/state change errors are correctly handled: errors during the initial connection hello are ignored' => 'PHPC does not implement CMAP',
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
        // Command monitoring event serverConnectionId is not yet implemented
        'command-monitoring/pre-42-server-connection-id: command events do not include server connection id' => 'Not yet implemented (PHPC-1899, PHPLIB-718)',
        'command-monitoring/server-connection-id: command events include server connection id' => 'Not yet implemented (PHPC-1899, PHPLIB-718)',
        // Change stream "comment" option is not yet implemented
        'change-streams/change-streams: Test with document comment' => 'Not yet implemented (PHPLIB-749)',
        'change-streams/change-streams: Test with document comment - pre 4.4' => 'Not yet implemented (PHPLIB-749)',
        'change-streams/change-streams: Test with string comment' => 'Not yet implemented (PHPLIB-749)',
        'change-streams/change-streams: Test that comment is set on getMore' => 'Not yet implemented (PHPLIB-749)',
        'change-streams/change-streams: Test that comment is not set on getMore - pre 4.4' => 'Not yet implemented (PHPLIB-749)',
        // CRUD "comment" option is not yet implemented
        'crud/bulkWrite-comment: BulkWrite with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/bulkWrite-comment: BulkWrite with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/bulkWrite-comment: BulkWrite with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/deleteMany-comment: deleteMany with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/deleteMany-comment: deleteMany with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/deleteMany-comment: deleteMany with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/deleteOne-comment: deleteOne with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/deleteOne-comment: deleteOne with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/deleteOne-comment: deleteOne with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/find-comment: find with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/find-comment: find with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/find-comment: find with document comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/find-comment: find with comment sets comment on getMore' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndDelete-comment: findOneAndDelete with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndDelete-comment: findOneAndDelete with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndDelete-comment: findOneAndDelete with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndReplace-comment: findOneAndReplace with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndReplace-comment: findOneAndReplace with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndReplace-comment: findOneAndReplace with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndUpdate-comment: findOneAndUpdate with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndUpdate-comment: findOneAndUpdate with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/findOneAndUpdate-comment: findOneAndUpdate with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/insertMany-comment: insertMany with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/insertMany-comment: insertMany with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/insertMany-comment: insertMany with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/insertOne-comment: insertOne with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/insertOne-comment: insertOne with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/insertOne-comment: insertOne with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/replaceOne-comment: ReplaceOne with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/replaceOne-comment: ReplaceOne with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/replaceOne-comment: ReplaceOne with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/updateMany-comment: UpdateMany with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/updateMany-comment: UpdateMany with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/updateMany-comment: UpdateMany with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/updateOne-comment: UpdateOne with string comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/updateOne-comment: UpdateOne with document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/updateOne-comment: UpdateOne with comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/aggregate: aggregate with comment sets comment on getMore' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/aggregate: aggregate with a document comment' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        'crud/aggregate: aggregate with a document comment - pre 4.4' => 'Not yet implemented (PHPC-2049, PHPLIB-749)',
        // CRUD "let" option is not yet implemented
        'crud/BulkWrite deleteMany-let: BulkWrite deleteMany-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite deleteMany-let: BulkWrite deleteMany with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite deleteMany-let: BulkWrite deleteMany with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite deleteOne-let: BulkWrite deleteOne-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite deleteOne-let: BulkWrite deleteOne with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite deleteOne-let: BulkWrite deleteOne with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite replaceOne-let: BulkWrite replaceOne-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite replaceOne-let: BulkWrite replaceOne with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite replaceOne-let: BulkWrite replaceOne with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite updateMany-let: BulkWrite updateMany-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite updateMany-let: BulkWrite updateMany with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite updateMany-let: BulkWrite updateMany with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite updateOne-let: BulkWrite updateOne-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite updateOne-let: BulkWrite updateOne with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/BulkWrite updateOne-let: BulkWrite updateOne with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/deleteMany-let: deleteMany-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/deleteMany-let: deleteMany with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/deleteMany-let: deleteMany with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/deleteOne-let: deleteOne-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/deleteOne-let: deleteOne with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/deleteOne-let: deleteOne with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/find-let: find-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/find-let: Find with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/find-let: Find with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndDelete-let: findOneAndDelete-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndDelete-let: findOneAndDelete with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndDelete-let: findOneAndDelete with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndReplace-let: findOneAndReplace-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndReplace-let: findOneAndReplace with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndReplace-let: findOneAndReplace with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndUpdate-let: findOneAndUpdate-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndUpdate-let: findOneAndUpdate with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/findOneAndUpdate-let: findOneAndUpdate with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/replaceOne-let: replaceOne-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/replaceOne-let: ReplaceOne with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/replaceOne-let: ReplaceOne with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/updateMany-let: updateMany-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/updateMany-let: updateMany with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/updateMany-let: updateMany with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
        'crud/updateOne-let: updateOne-let' => 'Not yet implemented (PHPLIB-748)',
        'crud/updateOne-let: UpdateOne with let option' => 'Not yet implemented (PHPLIB-748)',
        'crud/updateOne-let: UpdateOne with let option unsupported (server-side error)' => 'Not yet implemented (PHPLIB-748)',
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
     * @group serverless
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
     * @group serverless
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
     * @group serverless
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
            /* As is done in PHPUnit\Framework\TestCase::runBare(), exceptions
             * other than a select few will indicate a test failure. We cannot
             * call TestCase::hasFailed() because runBare() has yet to catch the
             * exceptions and update the TestCase's status.
             *
             * IncompleteTest is intentionally omitted as it is thrown for an
             * incompatible schema. This differs from PHPUnit's internal logic.
             */
            $failed = ! ($e instanceof SkippedTest || $e instanceof Warning);
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
