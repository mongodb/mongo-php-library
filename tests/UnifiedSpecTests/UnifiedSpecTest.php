<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use Exception;
use Generator;
use MongoDB\Tests\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
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
    private static array $incompleteTests = [
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
        // listDatabaseObjects is not implemented
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after InterruptedAtShutdown' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after InterruptedDueToReplStateChange' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after NotWritablePrimary' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after NotPrimaryNoSecondaryOk' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after NotPrimaryOrSecondary' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after PrimarySteppedDown' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after ShutdownInProgress' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after HostNotFound' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after HostUnreachable' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after NetworkTimeout' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects succeeds after SocketException' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects fails after two NotWritablePrimary errors' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects-serverErrors: ListDatabaseObjects fails after NotWritablePrimary when retryReads is false' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects: ListDatabaseObjects succeeds on first attempt' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects: ListDatabaseObjects succeeds on second attempt' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects: ListDatabaseObjects fails on first attempt' => 'listDatabaseObjects is not implemented',
        'retryable-reads/listDatabaseObjects: ListDatabaseObjects fails on second attempt' => 'listDatabaseObjects is not implemented',
        // listCollectionObjects is not implemented
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after InterruptedAtShutdown' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after InterruptedDueToReplStateChange' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after NotWritablePrimary' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after NotPrimaryNoSecondaryOk' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after NotPrimaryOrSecondary' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after PrimarySteppedDown' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after ShutdownInProgress' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after HostNotFound' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after HostUnreachable' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after NetworkTimeout' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects succeeds after SocketException' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects fails after two NotWritablePrimary errors' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects-serverErrors: ListCollectionObjects fails after NotWritablePrimary when retryReads is false' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects: ListCollectionObjects succeeds on first attempt' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects: ListCollectionObjects succeeds on second attempt' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects: ListCollectionObjects fails on first attempt' => 'listCollectionObjects is not implemented',
        'retryable-reads/listCollectionObjects: ListCollectionObjects fails on second attempt' => 'listCollectionObjects is not implemented',
        // listIndexNames is not implemented
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after InterruptedAtShutdown' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after InterruptedDueToReplStateChange' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after NotWritablePrimary' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after NotPrimaryNoSecondaryOk' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after NotPrimaryOrSecondary' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after PrimarySteppedDown' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after ShutdownInProgress' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after HostNotFound' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after HostUnreachable' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after NetworkTimeout' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames succeeds after SocketException' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames fails after two NotWritablePrimary errors' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames-serverErrors: ListIndexNames fails after NotWritablePrimary when retryReads is false' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames: ListIndexNames succeeds on first attempt' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames: ListIndexNames succeeds on second attempt' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames: ListIndexNames fails on first attempt' => 'listIndexNames is not implemented',
        'retryable-reads/listIndexNames: ListIndexNames fails on second attempt' => 'listIndexNames is not implemented',
        // mongoc_cluster_stream_for_server does not retry handshakes (CDRIVER-4532, PHPLIB-1033, PHPLIB-1042)
        'retryable-reads/retryable reads handshake failures: client.listDatabases succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: client.listDatabases succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: client.listDatabaseNames succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: client.listDatabaseNames succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: client.createChangeStream succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: client.createChangeStream succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.aggregate succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.aggregate succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.listCollections succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.listCollections succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.listCollectionNames succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.listCollectionNames succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.createChangeStream succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: database.createChangeStream succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.aggregate succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.aggregate succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.countDocuments succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.countDocuments succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.estimatedDocumentCount succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.estimatedDocumentCount succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.distinct succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.distinct succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.find succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.find succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.findOne succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.findOne succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.listIndexes succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.listIndexes succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.listIndexNames succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.listIndexNames succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.createChangeStream succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-reads/retryable reads handshake failures: collection.createChangeStream succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.insertOne succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.insertOne succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.insertMany succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.insertMany succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.deleteOne succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.deleteOne succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.replaceOne succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.replaceOne succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.updateOne succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.updateOne succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.findOneAndDelete succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.findOneAndDelete succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.findOneAndReplace succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.findOneAndReplace succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.findOneAndUpdate succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.findOneAndUpdate succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.bulkWrite succeeds after retryable handshake network error' => 'Handshakes are not retried (CDRIVER-4532)',
        'retryable-writes/retryable writes handshake failures: collection.bulkWrite succeeds after retryable handshake server error (ShutdownInProgress)' => 'Handshakes are not retried (CDRIVER-4532)',
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
        'valid-pass/createEntities-operation: createEntities operation' => 'CSOT is not yet implemented (PHPC-1760)',
        'valid-pass/entity-cursor-iterateOnce: iterateOnce' => 'CSOT is not yet implemented (PHPC-1760)',
        'valid-pass/matches-lte-operator: special lte matching operator' => 'CSOT is not yet implemented (PHPC-1760)',
        // commandCursor API is not yet implemented (PHPLIB-1077)
        'valid-pass/entity-commandCursor: runCursorCommand creates and exhausts cursor by running getMores' => 'commandCursor API is not yet implemented (PHPLIB-1077)',
        'valid-pass/entity-commandCursor: createCommandCursor creates a cursor and stores it as an entity that can be iterated one document at a time' => 'commandCursor API is not yet implemented (PHPLIB-1077)',
        'valid-pass/entity-commandCursor: createCommandCursor\'s cursor can be closed and will perform a killCursors operation' => 'commandCursor API is not yet implemented (PHPLIB-1077)',
        // libmongoc always adds readConcern to aggregate command
        'index-management/search index operations ignore read and write concern: listSearchIndexes ignores read and write concern' => 'libmongoc appends readConcern to aggregate command',
        // Tests need updating of the spec test
        'crud/aggregate-write-readPreference: Aggregate with $out includes read preference for 5.0+ server' => 'PHPLIB-1458',
        'crud/aggregate-write-readPreference: Aggregate with $out omits read preference for pre-5.0 server' => 'PHPLIB-1458',
        'crud/aggregate-write-readPreference: Aggregate with $merge includes read preference for 5.0+ server' => 'PHPLIB-1458',
        'crud/aggregate-write-readPreference: Aggregate with $merge omits read preference for pre-5.0 server' => 'PHPLIB-1458',
        'crud/db-aggregate-write-readPreference: Database-level aggregate with $out includes read preference for 5.0+ server' => 'PHPLIB-1458',
        'crud/db-aggregate-write-readPreference: Database-level aggregate with $out omits read preference for pre-5.0 server' => 'PHPLIB-1458',
        'crud/db-aggregate-write-readPreference: Database-level aggregate with $merge includes read preference for 5.0+ server' => 'PHPLIB-1458',
        'crud/db-aggregate-write-readPreference: Database-level aggregate with $merge omits read preference for pre-5.0 server' => 'PHPLIB-1458',
    ];

    /**
     * Any tests that rely on session pinning (including targetedFailPoint) must
     * be skipped since libmongoc does not pin on load-balanced toplogies. */
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
        return self::provideTests('atlas-data-lake');
    }

    #[DataProvider('provideChangeStreamsTests')]
    #[Group('serverless')]
    public function testChangeStreams(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideChangeStreamsTests(): Generator
    {
        return self::provideTests('change-streams');
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
        return self::provideTests('client-side-encryption');
    }

    #[DataProvider('provideCollectionManagementTests')]
    #[Group('serverless')]
    public function testCollectionManagement(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideCollectionManagementTests(): Generator
    {
        return self::provideTests('collection-management');
    }

    #[DataProvider('provideCommandMonitoringTests')]
    #[Group('serverless')]
    public function testCommandMonitoring(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideCommandMonitoringTests(): Generator
    {
        return self::provideTests('command-monitoring');
    }

    #[DataProvider('provideCrudTests')]
    #[Group('serverless')]
    public function testCrud(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideCrudTests(): Generator
    {
        return self::provideTests('crud');
    }

    #[DataProvider('provideGridFSTests')]
    #[Group('serverless')]
    public function testGridFS(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideGridFSTests(): Generator
    {
        return self::provideTests('gridfs');
    }

    #[DataProvider('provideLoadBalancers')]
    #[Group('serverless')]
    public function testLoadBalancers(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideLoadBalancers(): Generator
    {
        return self::provideTests('load-balancers');
    }

    #[DataProvider('provideReadWriteConcernTests')]
    public function testReadWriteConcern(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideReadWriteConcernTests(): Generator
    {
        return self::provideTests('read-write-concern');
    }

    #[DataProvider('provideRetryableReadsTests')]
    #[Group('serverless')]
    public function testRetryableReads(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideRetryableReadsTests(): Generator
    {
        return self::provideTests('retryable-reads');
    }

    #[DataProvider('provideRetryableWritesTests')]
    #[Group('serverless')]
    public function testRetryableWrites(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideRetryableWritesTests(): Generator
    {
        return self::provideTests('retryable-writes');
    }

    #[DataProvider('provideRunCommandTests')]
    #[Group('serverless')]
    public function testRunCommand(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideRunCommandTests(): Generator
    {
        return self::provideTests('run-command');
    }

    #[DataProvider('provideSessionsTests')]
    #[Group('serverless')]
    public function testSessions(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideSessionsTests(): Generator
    {
        return self::provideTests('sessions');
    }

    #[DataProvider('provideTransactionsTests')]
    #[Group('serverless')]
    public function testTransactions(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideTransactionsTests(): Generator
    {
        return self::provideTests('transactions');
    }

    #[DataProvider('provideTransactionsConvenientApiTests')]
    public function testTransactionsConvenientApi(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function provideTransactionsConvenientApiTests(): Generator
    {
        return self::provideTests('transactions-convenient-api');
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
        return self::provideTests('versioned-api');
    }

    #[DataProvider('providePassingTests')]
    public function testPassingTests(UnifiedTestCase $test): void
    {
        self::$runner->run($test);
    }

    public static function providePassingTests(): Generator
    {
        yield from self::provideTests('valid-pass');
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
        yield from self::provideTests('valid-fail');
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
        yield from self::provideTests('index-management');
    }

    private static function provideTests(string $directory): Generator
    {
        $pattern = self::$testDir . '/' . $directory . '/*.json';

        foreach (glob($pattern) as $filename) {
            $group = basename(dirname($filename));

            foreach (UnifiedTestCase::fromFile($filename) as $name => $test) {
                yield $group . '/' . $name => [$test];
            }
        }
    }
}
