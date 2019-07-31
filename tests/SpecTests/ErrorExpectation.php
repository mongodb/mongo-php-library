<?php

namespace MongoDB\Tests\SpecTests;

use Exception;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;
use stdClass;
use function get_class;
use function is_array;
use function is_string;
use function sprintf;

/**
 * Spec test operation error expectation.
 */
final class ErrorExpectation
{
    /**
     * @see https://github.com/mongodb/mongo/blob/master/src/mongo/base/error_codes.err
     *
     * @var array
     */
    private static $codeNameMap = [
        'Interrupted' => 11601,
        'MaxTimeMSExpired' => 50,
        'NoSuchTransaction' => 251,
        'OperationNotSupportedInTransaction' => 263,
        'WriteConflict' => 112,
    ];

    /** @var integer */
    private $code;

    /** @var string */
    private $codeName;

    /** @var boolean */
    private $isExpected = false;

    /** @var string[] */
    private $excludedLabels = [];

    /** @var string[] */
    private $includedLabels = [];

    /** @var string */
    private $messageContains;

    private function __construct()
    {
    }

    public static function fromChangeStreams(stdClass $result)
    {
        $o = new self();

        if (isset($result->error->code)) {
            $o->code = $result->error->code;
            $o->isExpected = true;
        }

        if (isset($result->error->errorLabels)) {
            if (! self::isArrayOfStrings($result->error->errorLabels)) {
                throw InvalidArgumentException::invalidType('errorLabels', $result->error->errorLabels, 'string[]');
            }
            $o->includedLabels = $result->error->errorLabels;
            $o->isExpected = true;
        }

        return $o;
    }

    public static function fromRetryableReads(stdClass $operation)
    {
        $o = new self();

        if (isset($operation->error)) {
            $o->isExpected = $operation->error;
        }

        return $o;
    }

    public static function fromRetryableWrites(stdClass $outcome)
    {
        $o = new self();

        if (isset($outcome->error)) {
            $o->isExpected = $outcome->error;
        }

        return $o;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromTransactions(stdClass $operation)
    {
        $o = new self();

        if (isset($operation->error)) {
            $o->isExpected = $operation->error;
        }

        $result = isset($operation->result) ? $operation->result : null;

        if (isset($result->errorContains)) {
            $o->messageContains = $result->errorContains;
            $o->isExpected = true;
        }

        if (isset($result->errorCodeName)) {
            $o->codeName = $result->errorCodeName;
            $o->isExpected = true;
        }

        if (isset($result->errorLabelsContain)) {
            if (! self::isArrayOfStrings($result->errorLabelsContain)) {
                throw InvalidArgumentException::invalidType('errorLabelsContain', $result->errorLabelsContain, 'string[]');
            }
            $o->includedLabels = $result->errorLabelsContain;
            $o->isExpected = true;
        }

        if (isset($result->errorLabelsOmit)) {
            if (! self::isArrayOfStrings($result->errorLabelsOmit)) {
                throw InvalidArgumentException::invalidType('errorLabelsOmit', $result->errorLabelsOmit, 'string[]');
            }
            $o->excludedLabels = $result->errorLabelsOmit;
            $o->isExpected = true;
        }

        return $o;
    }

    public static function noError()
    {
        return new self();
    }

    /**
     * Assert that the error expectation matches the actual outcome.
     *
     * @param TestCase       $test   Test instance for performing assertions
     * @param Exception|null $actual Exception (if any) from the actual outcome
     */
    public function assert(TestCase $test, Exception $actual = null)
    {
        if (! $this->isExpected) {
            if ($actual !== null) {
                $test->fail(sprintf("Operation threw unexpected %s: %s\n%s", get_class($actual), $actual->getMessage(), $actual->getTraceAsString()));
            }

            return;
        }

        $test->assertNotNull($actual);

        if (isset($this->messageContains)) {
            $test->assertStringContainsStringIgnoringCase($this->messageContains, $actual->getMessage());
        }

        if (isset($this->codeName)) {
            $this->assertCodeName($test, $actual);
        }

        if (! empty($this->excludedLabels) || ! empty($this->includedLabels)) {
            $test->assertInstanceOf(RuntimeException::class, $actual);

            foreach ($this->excludedLabels as $label) {
                $test->assertFalse($actual->hasErrorLabel($label), 'Exception should not have error label: ' . $label);
            }

            foreach ($this->includedLabels as $label) {
                $test->assertTrue($actual->hasErrorLabel($label), 'Exception should have error label: ' . $label);
            }
        }
    }

    public function isExpected()
    {
        return $this->isExpected;
    }

    /**
     * Assert that the error code name expectation matches the actual outcome.
     *
     * @param TestCase       $test   Test instance for performing assertions
     * @param Exception|null $actual Exception (if any) from the actual outcome
     */
    private function assertCodeName(TestCase $test, Exception $actual = null)
    {
        /* BulkWriteException does not expose codeName for server errors. Work
         * around this be comparing the error code against a map.
         *
         * TODO: Remove this once PHPC-1386 is resolved. */
        if ($actual instanceof BulkWriteException || $actual instanceof ExecutionTimeoutException) {
            $test->assertArrayHasKey($this->codeName, self::$codeNameMap);
            $test->assertSame(self::$codeNameMap[$this->codeName], $actual->getCode());

            return;
        }

        $test->assertInstanceOf(CommandException::class, $actual);
        $result = $actual->getResultDocument();

        if (isset($result->writeConcernError)) {
            $test->assertObjectHasAttribute('codeName', $result->writeConcernError);
            $test->assertSame($this->codeName, $result->writeConcernError->codeName);

            return;
        }

        $test->assertObjectHasAttribute('codeName', $result);
        $test->assertSame($this->codeName, $result->codeName);
    }

    private static function isArrayOfStrings($array)
    {
        if (! is_array($array)) {
            return false;
        }

        foreach ($array as $string) {
            if (! is_string($string)) {
                return false;
            }
        }

        return true;
    }
}
