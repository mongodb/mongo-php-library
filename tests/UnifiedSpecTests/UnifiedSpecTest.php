<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use Exception;
use Generator;
use MongoDB\Tests\FunctionalTestCase;
use PHPUnit\Framework\SkippedTest;
use PHPUnit\Framework\Warning;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function glob;

/**
 * Unified test format spec tests.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst
 */
class UnifiedSpecTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var UnifiedTestRunner */
    private static $runner;

    private static function doSetUpBeforeClass()
    {
        parent::setUpBeforeClass();

        /* Provide unmodified URI for internal client, since it may need to
         * execute commands on multiple mongoses (e.g. killAllSessions) */
        self::$runner = new UnifiedTestRunner(static::getUri(true));
    }

    /**
     * @dataProvider provideChangeStreamsTests
     */
    public function testChangeStreams(UnifiedTestCase $test)
    {
        self::$runner->run($test);
    }

    public function provideChangeStreamsTests()
    {
        return $this->provideTests(__DIR__ . '/change-streams/*.json');
    }

    /**
     * @dataProvider provideCrudTests
     */
    public function testCrud(UnifiedTestCase $test)
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
    public function testGridFS(UnifiedTestCase $test)
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
    public function testTransactions(UnifiedTestCase $test)
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
    public function testVersionedApi(UnifiedTestCase $test)
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
    public function testPassingTests(UnifiedTestCase $test)
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
    public function testFailingTests(UnifiedTestCase $test)
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

    private function provideTests(string $pattern) : Generator
    {
        foreach (glob($pattern) as $filename) {
            foreach (UnifiedTestCase::fromFile($filename) as $name => $test) {
                yield $name => [$test];
            }
        }
    }
}
