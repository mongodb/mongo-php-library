<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Exception\BulkWriteCommandException;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Tests\UnifiedSpecTests\Constraint\Matches;
use PHPUnit\Framework\Assert;
use stdClass;
use Throwable;

use function count;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsBool;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotInstanceOf;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectHasProperty;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\logicalOr;
use function property_exists;
use function sprintf;

final class ExpectedError
{
    /** @see https://github.com/mongodb/mongo/blob/master/src/mongo/base/error_codes.err */
    private static array $codeNameMap = [
        'Interrupted' => 11601,
        'MaxTimeMSExpired' => 50,
        'NoSuchTransaction' => 251,
        'OperationNotSupportedInTransaction' => 263,
        'WriteConflict' => 112,
    ];

    private bool $isError = false;

    private ?bool $isClientError = null;

    private ?string $messageContains = null;

    private ?int $code = null;

    private ?string $codeName = null;

    private ?Matches $matchesErrorResponse = null;

    private array $includedLabels = [];

    private array $excludedLabels = [];

    private ?ExpectedResult $expectedResult = null;

    private ?array $writeErrors = null;

    private ?array $writeConcernErrors = null;

    public function __construct(?stdClass $o, EntityMap $entityMap)
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

        if (property_exists($o, 'isTimeoutError')) {
            Assert::markTestIncomplete('CSOT is not yet implemented (PHPC-1760)');
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

        if (isset($o->errorResponse)) {
            assertIsObject($o->errorResponse);
            $this->matchesErrorResponse = new Matches($o->errorResponse, $entityMap);
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

        if (isset($o->writeErrors)) {
            assertIsObject($o->writeErrors);
            assertContainsOnly('object', (array) $o->writeErrors);

            foreach ($o->writeErrors as $i => $writeError) {
                $this->writeErrors[$i] = new Matches($writeError, $entityMap);
            }
        }

        if (isset($o->writeConcernErrors)) {
            assertIsArray($o->writeConcernErrors);
            assertContainsOnly('object', $o->writeConcernErrors);

            foreach ($o->writeConcernErrors as $i => $writeConcernError) {
                $this->writeConcernErrors[$i] = new Matches($writeConcernError, $entityMap);
            }
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
            Assert::fail(sprintf("Operation threw unexpected %s: %s\n%s", $e::class, $e->getMessage(), $e->getTraceAsString()));
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

        if (isset($this->matchesErrorResponse)) {
            assertThat($e, logicalOr(
                isInstanceOf(CommandException::class),
                isInstanceOf(BulkWriteException::class),
                isInstanceOf(BulkWriteCommandException::class),
            ));

            if ($e instanceof CommandException) {
                assertThat($e->getResultDocument(), $this->matchesErrorResponse, 'CommandException result document matches expected errorResponse');
            } elseif ($e instanceof BulkWriteCommandException) {
                assertThat($e->getErrorReply(), $this->matchesErrorResponse, 'BulkWriteCommandException error reply matches expected errorResponse');
            } elseif ($e instanceof BulkWriteException) {
                $writeErrors = $e->getWriteResult()->getErrorReplies();
                assertCount(1, $writeErrors);
                assertThat($writeErrors[0], $this->matchesErrorResponse, 'BulkWriteException first error reply matches expected errorResponse');
            }
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
            assertThat($e, logicalOr(
                isInstanceOf(BulkWriteException::class),
                isInstanceOf(BulkWriteCommandException::class),
            ));

            if ($e instanceof BulkWriteCommandException) {
                $this->expectedResult->assert($e->getPartialResult());
            } elseif ($e instanceof BulkWriteException) {
                $this->expectedResult->assert($e->getWriteResult());
            }
        }

        if (isset($this->writeErrors)) {
            assertInstanceOf(BulkWriteCommandException::class, $e);
            $this->assertWriteErrors($e->getWriteErrors());
        }

        if (isset($this->writeConcernErrors)) {
            assertInstanceOf(BulkWriteCommandException::class, $e);
            $this->assertWriteConcernErrors($e->getWriteConcernErrors());
        }
    }

    private function assertIsClientError(Throwable $e): void
    {
        /* Note: BulkWriteException and BulkWriteCommandException may proxy a
         * previous exception. Unwrap it to check the original error. */
        if (($e instanceof BulkWriteException || $e instanceof BulkWriteCommandException) && $e->getPrevious() !== null) {
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
            assertObjectHasProperty('codeName', $result->writeConcernError);
            assertSame($this->codeName, $result->writeConcernError->codeName);

            return;
        }

        assertObjectHasProperty('codeName', $result);
        assertSame($this->codeName, $result->codeName);
    }

    private function assertWriteErrors(array $writeErrors): void
    {
        assertCount(count($this->writeErrors), $writeErrors);

        foreach ($this->writeErrors as $i => $matchesWriteError) {
            assertArrayHasKey($i, $writeErrors);
            $writeError = $writeErrors[$i];

            // Not required by the spec test, but asserts PHPC correctness
            assertSame((int) $i, $writeError->getIndex());

            /* Convert the WriteError into a document for matching. These
             * field names are derived from the CRUD spec. */
            $writeErrorDocument = [
                'code' => $writeError->getCode(),
                'message' => $writeError->getMessage(),
                'details' => $writeError->getInfo(),
            ];

            assertThat($writeErrorDocument, $matchesWriteError);
        }
    }

    private function assertWriteConcernErrors(array $writeConcernErrors): void
    {
        assertCount(count($this->writeConcernErrors), $writeConcernErrors);

        foreach ($this->writeConcernErrors as $i => $matchesWriteConcernError) {
            assertArrayHasKey($i, $writeConcernErrors);
            $writeConcernError = $writeConcernErrors[$i];

            /* Convert the WriteConcernError into a document for matching.
             * These field names are derived from the CRUD spec. */
            $writeConcernErrorDocument = [
                'code' => $writeConcernError->getCode(),
                'message' => $writeConcernError->getMessage(),
                'details' => $writeConcernError->getInfo(),
            ];

            assertThat($writeConcernErrorDocument, $matchesWriteConcernError);
        }
    }
}
