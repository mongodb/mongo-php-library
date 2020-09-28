<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use stdClass;
use Throwable;
use function get_class;
use function sprintf;

final class ExpectedError
{
    /**
     * @see https://github.com/mongodb/mongo/blob/master/src/mongo/base/error_codes.err
     * @var array
     */
    private static $codeNameMap = [
        'Interrupted' => 11601,
        'MaxTimeMSExpired' => 50,
        'NoSuchTransaction' => 251,
        'OperationNotSupportedInTransaction' => 263,
        'WriteConflict' => 112,
    ];

    /** @var bool */
    private $isError = true;

    /** @var bool */
    private $isClientError;

    /** @var string */
    private $messageContains;

    /** @var int */
    private $code;

    /** @var string */
    private $codeName;

    /** @var array */
    private $includedLabels = [];

    /** @var array */
    private $excludedLabels = [];

    /** @var ExpectedResult */
    private $expectedResult;

    private function __construct(stdClass $o = null)
    {
        if (isset($o->isError)) {
            assertTrue($o->isError);
        }

        if (isset($o->isClientError)) {
            assertInternalType('bool', $o->isClientError);
            $this->isClientError = $o->isClientError;
        }

        if (isset($o->errorContains)) {
            assertInternalType('string', $o->errorContains);
            $this->messageContains = $o->errorContains;
        }

        if (isset($o->errorCode)) {
            assertInternalType('int', $o->errorCode);
            $this->code = $o->errorCode;
        }

        if (isset($o->errorCodeName)) {
            assertInternalType('string', $o->errorCodeName);
            $this->codeName = $o->errorCodeName;
        }

        if (isset($o->errorLabelsContain)) {
            assertInternalType('array', $o->errorLabelsContain);
            assertContainsOnly('string', $o->errorLabelsContain);
            $o->includedLabels = $o->errorLabelsContain;
        }

        if (isset($o->errorLabelsOmit)) {
            assertInternalType('array', $o->errorLabelsOmit);
            assertContainsOnly('string', $o->errorLabelsOmit);
            $o->excludedLabels = $o->errorLabelsOmit;
        }

        if (isset($o->expectedResult)) {
            $o->expectedResult = new ExpectedResult($o->expectResult);
        }
    }

    public static function fromOperation(stdClass $o) : self
    {
        if (! isset($o->expectError)) {
            $expectedError = new self();
            $expectedError->isError = false;

            return $expectedError;
        }

        $expectedError = new self($o->expectError);

        if (isset($o->expectError->expectResult)) {
            $o->expectResult = ExpectedResult::fromOperation($o);
        }
    }

    /**
     * Assert the outcome of an operation.
     *
     * @param Throwable|null $e Exception (if any) from executing an operation
     */
    public function assert(Throwable $e = null)
    {
        if (! $this->isError && $e !== null) {
            Assert::fail(sprintf("Operation threw unexpected %s: %s\n%s", get_class($e), $e->getMessage(), $e->getTraceAsString()));
        }

        if (! $this->isError) {
            assertNull($e);

            return;
        }

        assertNotNull($e);

        if (isset($this->messageContains)) {
            assertStringContainsStringIgnoringCase($this->messageContains, $e->getMessage());
        }

        if (isset($this->code)) {
            assertInstanceOf(ServerException::class, $e);
            assertSame($this->code, $e->getCode());
        }

        if (isset($this->codeName)) {
            assertInstanceOf(ServerException::class, $e);
            $this->assertCodeName($e);
        }

        if (! empty($this->excludedLabels) || ! empty($this->includedLabels)) {
            assertInstanceOf(RuntimeException::class, $e);

            foreach ($this->excludedLabels as $label) {
                assertFalse($e->hasErrorLabel($label), 'Exception should not have error label: ' . $label);
            }

            foreach ($this->includedLabels as $label) {
                assertTrue($e->hasErrorLabel($label), 'Exception should have error label: ' . $label);
            }
        }

        if (isset($this->expectedResult)) {
            assertInstanceOf(BulkWriteException::class, $e);
            $this->expectedResult->assert($e->getWriteResult());
        }
    }

    private function assertCodeName(ServerException $e)
    {
        /* BulkWriteException and ExecutionTimeoutException do not expose
         * codeName. Work around this by translating it to a numeric code.
         *
         * TODO: Remove this once PHPC-1386 is resolved. */
        if ($e instanceof BulkWriteException || $e instanceof ExecutionTimeoutException) {
            assertArrayHasKey($this->codeName, self::$codeNameMap);
            assertSame(self::$codeNameMap[$this->codeName], $e->getCode());

            return;
        }

        assertInstanceOf(CommandException::class, $e);
        $result = $e->getResultDocument();

        if (isset($result->writeConcernError)) {
            assertObjectHasAttribute('codeName', $result->writeConcernError);
            assertSame($this->codeName, $result->writeConcernError->codeName);

            return;
        }

        assertObjectHasAttribute('codeName', $result);
        assertSame($this->codeName, $result->codeName);
    }
}
