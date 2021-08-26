<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use PHPUnit\Framework\Assert;
use stdClass;
use Throwable;

use function get_class;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsBool;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotInstanceOf;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectHasAttribute;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use function PHPUnit\Framework\assertTrue;
use function property_exists;
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
    private $isError = false;

    /** @var bool|null */
    private $isClientError;

    /** @var string|null */
    private $messageContains;

    /** @var int|null */
    private $code;

    /** @var string|null */
    private $codeName;

    /** @var array */
    private $includedLabels = [];

    /** @var array */
    private $excludedLabels = [];

    /** @var ExpectedResult|null */
    private $expectedResult;

    public function __construct(?stdClass $o = null, EntityMap $entityMap)
    {
        if ($o === null) {
            return;
        }

        $this->isError = true;

        if (isset($o->isError)) {
            assertTrue($o->isError);
        }

        if (isset($o->isClientError)) {
            assertIsBool($o->isClientError);
            $this->isClientError = $o->isClientError;
        }

        if (isset($o->errorContains)) {
            assertIsString($o->errorContains);
            $this->messageContains = $o->errorContains;
        }

        if (isset($o->errorCode)) {
            assertIsInt($o->errorCode);
            $this->code = $o->errorCode;
        }

        if (isset($o->errorCodeName)) {
            assertIsString($o->errorCodeName);
            $this->codeName = $o->errorCodeName;
        }

        if (isset($o->errorLabelsContain)) {
            assertIsArray($o->errorLabelsContain);
            assertContainsOnly('string', $o->errorLabelsContain);
            $this->includedLabels = $o->errorLabelsContain;
        }

        if (isset($o->errorLabelsOmit)) {
            assertIsArray($o->errorLabelsOmit);
            assertContainsOnly('string', $o->errorLabelsOmit);
            $this->excludedLabels = $o->errorLabelsOmit;
        }

        if (property_exists($o, 'expectResult')) {
            $this->expectedResult = new ExpectedResult($o, $entityMap);
        }
    }

    /**
     * Assert the outcome of an operation.
     *
     * @param Throwable|null $e Exception (if any) from executing an operation
     */
    public function assert(?Throwable $e = null): void
    {
        if (! $this->isError && $e !== null) {
            Assert::fail(sprintf("Operation threw unexpected %s: %s\n%s", get_class($e), $e->getMessage(), $e->getTraceAsString()));
        }

        if (! $this->isError) {
            assertNull($e);

            return;
        }

        assertNotNull($e);

        if (isset($this->isClientError)) {
            $this->assertIsClientError($e);
        }

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

    private function assertIsClientError(Throwable $e): void
    {
        /* Note: BulkWriteException may proxy a previous exception. Unwrap it
         * to check the original error. */
        if ($e instanceof BulkWriteException && $e->getPrevious() !== null) {
            $e = $e->getPrevious();
        }

        if ($this->isClientError) {
            assertNotInstanceOf(ServerException::class, $e);
        } else {
            assertInstanceOf(ServerException::class, $e);
        }
    }

    private function assertCodeName(ServerException $e): void
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
