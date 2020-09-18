<?php

namespace MongoDB\Tests;

use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util\Filter;
use PHPUnit\Util\Printer;
use function get_class;
use function json_encode;
use function round;

// phpcs:disable SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
class EvergreenLogListener extends Printer implements TestListener
{
    /** @var array[] */
    private $tests = [];

    /** @var array|null */
    private $currentTestCase = null;

    /**
     * Flush buffer and close output.
     */
    public function flush()
    {
        $this->write($this->getJson());

        parent::flush();
    }

    /**
     * An error occurred.
     *
     * @param Test      $test
     * @param Exception $e
     * @param float     $time
     */
    public function addError(Test $test, Exception $e, $time)
    {
        $this->doAddFault($test, $e, 'fail');
    }

    /**
     * A warning occurred.
     *
     * @param Test    $test
     * @param Warning $e
     * @param float   $time
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        // Do nothing for now
    }

    /**
     * A failure occurred.
     *
     * @param Test                 $test
     * @param AssertionFailedError $e
     * @param float                $time
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->doAddFault($test, $e, 'fail');
    }

    /**
     * Incomplete test.
     *
     * @param Test      $test
     * @param Exception $e
     * @param float     $time
     */
    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
        $this->doAddSkipped($test);
    }

    /**
     * Risky test.
     *
     * @param Test      $test
     * @param Exception $e
     * @param float     $time
     */
    public function addRiskyTest(Test $test, Exception $e, $time)
    {
        return;
    }

    /**
     * Skipped test.
     *
     * @param Test      $test
     * @param Exception $e
     * @param float     $time
     */
    public function addSkippedTest(Test $test, Exception $e, $time)
    {
        $this->doAddSkipped($test);
    }

    /**
     * A testsuite started.
     *
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
    }

    /**
     * A testsuite ended.
     *
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite)
    {
    }

    /**
     * A test started.
     *
     * @param Test $test
     */
    public function startTest(Test $test)
    {
        $this->currentTestCase = [
            'status' => 'pass',
            'test_file' => get_class($test) . '::' . $test->getName(),
        ];
    }

    /**
     * A test ended.
     *
     * @param Test  $test
     * @param float $time
     */
    public function endTest(Test $test, $time)
    {
        if (! $this->currentTestCase) {
            return;
        }

        $this->currentTestCase['elapsed'] = round($time, 6);

        $this->tests[] = $this->currentTestCase;

        $this->currentTestCase = null;
    }

    /**
     * Returns the XML as a string.
     *
     * @return string
     */
    public function getJson()
    {
        return json_encode(['results' => $this->tests]);
    }

    /**
     * Method which generalizes addError() and addFailure()
     *
     * @param Test      $test
     * @param Exception $e
     * @param string    $type
     */
    private function doAddFault(Test $test, Exception $e, $type)
    {
        if ($this->currentTestCase === null) {
            return;
        }

        if ($test instanceof SelfDescribing) {
            $buffer = $test->toString() . "\n";
        } else {
            $buffer = '';
        }

        $buffer .= TestFailure::exceptionToString($e) . "\n" .
            Filter::getFilteredStacktrace($e);

        $this->currentTestCase['status'] = $type;
        $this->currentTestCase['raw_log'] = $buffer;
    }

    private function doAddSkipped(Test $test)
    {
        if ($this->currentTestCase === null) {
            return;
        }

        $this->currentTestCase['status'] = 'skip';
    }
}
// phpcs:enable SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
