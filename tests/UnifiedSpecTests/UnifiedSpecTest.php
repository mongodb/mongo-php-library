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
        // PHPLIB-573 and DRIVERS-1340
        'crud/unacknowledged-bulkWrite-delete-hint-clientError: Unacknowledged bulkWrite deleteOne with hints fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-bulkWrite-delete-hint-clientError: Unacknowledged bulkWrite deleteMany with hints fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-bulkWrite-update-hint-clientError: Unacknowledged bulkWrite updateOne with hints fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-bulkWrite-update-hint-clientError: Unacknowledged bulkWrite updateMany with hints fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-bulkWrite-update-hint-clientError: Unacknowledged bulkWrite replaceOne with hints fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-deleteMany-hint-clientError: Unacknowledged deleteMany with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-deleteMany-hint-clientError: Unacknowledged deleteMany with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-deleteOne-hint-clientError: Unacknowledged deleteOne with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-deleteOne-hint-clientError: Unacknowledged deleteOne with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-findOneAndDelete-hint-clientError: Unacknowledged findOneAndDelete with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-findOneAndDelete-hint-clientError: Unacknowledged findOneAndDelete with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-findOneAndReplace-hint-clientError: Unacknowledged findOneAndReplace with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-findOneAndReplace-hint-clientError: Unacknowledged findOneAndReplace with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-findOneAndUpdate-hint-clientError: Unacknowledged findOneAndUpdate with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-findOneAndUpdate-hint-clientError: Unacknowledged findOneAndUpdate with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-replaceOne-hint-clientError: Unacknowledged ReplaceOne with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-replaceOne-hint-clientError: Unacknowledged ReplaceOne with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-updateMany-hint-clientError: Unacknowledged updateMany with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-updateMany-hint-clientError: Unacknowledged updateMany with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-updateOne-hint-clientError: Unacknowledged updateOne with hint string fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        'crud/unacknowledged-updateOne-hint-clientError: Unacknowledged updateOne with hint document fails with client-side error' => 'PHPLIB-573 and DRIVERS-1340',
        // PHPC does not implement CMAP
        'valid-pass/assertNumberConnectionsCheckedOut: basic assertion succeeds' => 'PHPC does not implement CMAP',
        'valid-pass/entity-client-cmap-events: events are captured during an operation' => 'PHPC does not implement CMAP',
        'valid-pass/expectedEventsForClient-eventType: eventType can be set to command and cmap' => 'PHPC does not implement CMAP',
        'valid-pass/expectedEventsForClient-eventType: eventType defaults to command if unset' => 'PHPC does not implement CMAP',
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
