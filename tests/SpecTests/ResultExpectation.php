<?php

namespace MongoDB\Tests\SpecTests;

use LogicException;
use MongoDB\BulkWriteResult;
use MongoDB\DeleteResult;
use MongoDB\Driver\WriteResult;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\InsertManyResult;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use stdClass;
use function call_user_func;
use function is_array;
use function is_object;
use function property_exists;

/**
 * Spec test operation result expectation.
 */
final class ResultExpectation
{
    const ASSERT_NOTHING = 0;
    const ASSERT_BULKWRITE = 1;
    const ASSERT_DELETE = 2;
    const ASSERT_INSERTMANY = 3;
    const ASSERT_INSERTONE = 4;
    const ASSERT_UPDATE = 5;
    const ASSERT_SAME = 6;
    const ASSERT_SAME_DOCUMENT = 7;
    const ASSERT_SAME_DOCUMENTS = 8;
    const ASSERT_MATCHES_DOCUMENT = 9;
    const ASSERT_NULL = 10;
    const ASSERT_CALLABLE = 11;
    const ASSERT_DOCUMENTS_MATCH = 12;

    /** @var integer */
    private $assertionType = self::ASSERT_NOTHING;

    /** @var mixed */
    private $expectedValue;

    /** @var callable */
    private $assertionCallable;

    /**
     * @param integer $assertionType
     * @param mixed   $expectedValue
     */
    private function __construct($assertionType, $expectedValue)
    {
        switch ($assertionType) {
            case self::ASSERT_BULKWRITE:
            case self::ASSERT_DELETE:
            case self::ASSERT_INSERTMANY:
            case self::ASSERT_INSERTONE:
            case self::ASSERT_UPDATE:
                if (! is_object($expectedValue)) {
                    throw InvalidArgumentException::invalidType('$expectedValue', $expectedValue, 'object');
                }
                break;

            case self::ASSERT_SAME_DOCUMENTS:
                if (! self::isArrayOfObjects($expectedValue)) {
                    throw InvalidArgumentException::invalidType('$expectedValue', $expectedValue, 'object[]');
                }
                break;
        }

        $this->assertionType = $assertionType;
        $this->expectedValue = $expectedValue;
    }

    public static function fromChangeStreams(stdClass $result, callable $assertionCallable)
    {
        if (! property_exists($result, 'success')) {
            return new self(self::ASSERT_NOTHING, null);
        }

        $o = new self(self::ASSERT_CALLABLE, $result->success);

        $o->assertionCallable = $assertionCallable;

        return $o;
    }

    public static function fromClientSideEncryption(stdClass $operation, $defaultAssertionType)
    {
        if (property_exists($operation, 'result') && ! self::isErrorResult($operation->result)) {
            $assertionType = $operation->result === null ? self::ASSERT_NULL : $defaultAssertionType;
            $expectedValue = $operation->result;
        } else {
            $assertionType = self::ASSERT_NOTHING;
            $expectedValue = null;
        }

        return new self($assertionType, $expectedValue);
    }

    public static function fromCrud(stdClass $operation, $defaultAssertionType)
    {
        if (property_exists($operation, 'result') && ! self::isErrorResult($operation->result)) {
            $assertionType = $operation->result === null ? self::ASSERT_NULL : $defaultAssertionType;
            $expectedValue = $operation->result;
        } else {
            $assertionType = self::ASSERT_NOTHING;
            $expectedValue = null;
        }

        return new self($assertionType, $expectedValue);
    }

    public static function fromReadWriteConcern(stdClass $operation, $defaultAssertionType)
    {
        if (property_exists($operation, 'result') && ! self::isErrorResult($operation->result)) {
            $assertionType = $operation->result === null ? self::ASSERT_NULL : $defaultAssertionType;
            $expectedValue = $operation->result;
        } else {
            $assertionType = self::ASSERT_NOTHING;
            $expectedValue = null;
        }

        return new self($assertionType, $expectedValue);
    }

    public static function fromRetryableReads(stdClass $operation, $defaultAssertionType)
    {
        if (property_exists($operation, 'result') && ! self::isErrorResult($operation->result)) {
            $assertionType = $operation->result === null ? self::ASSERT_NULL : $defaultAssertionType;
            $expectedValue = $operation->result;
        } else {
            $assertionType = self::ASSERT_NOTHING;
            $expectedValue = null;
        }

        return new self($assertionType, $expectedValue);
    }

    public static function fromRetryableWrites(stdClass $outcome, $defaultAssertionType)
    {
        if (property_exists($outcome, 'result') && ! self::isErrorResult($outcome->result)) {
            $assertionType = $outcome->result === null ? self::ASSERT_NULL : $defaultAssertionType;
            $expectedValue = $outcome->result;
        } else {
            $assertionType = self::ASSERT_NOTHING;
            $expectedValue = null;
        }

        return new self($assertionType, $expectedValue);
    }

    public static function fromTransactions(stdClass $operation, $defaultAssertionType)
    {
        if (property_exists($operation, 'result') && ! self::isErrorResult($operation->result)) {
            $assertionType = $operation->result === null ? self::ASSERT_NULL : $defaultAssertionType;
            $expectedValue = $operation->result;
        } else {
            $assertionType = self::ASSERT_NOTHING;
            $expectedValue = null;
        }

        return new self($assertionType, $expectedValue);
    }

    /**
     * Assert that the result expectation matches the actual outcome.
     *
     * @param FunctionalTestCase $test   Test instance for performing assertions
     * @param mixed              $result Result (if any) from the actual outcome
     * @throws LogicException if the assertion type is unsupported
     */
    public function assert(FunctionalTestCase $test, $actual)
    {
        $expected = $this->expectedValue;

        switch ($this->assertionType) {
            case self::ASSERT_BULKWRITE:
                /* If the bulk write was successful, the actual value should be
                 * a BulkWriteResult; otherwise, expect a WriteResult extracted
                 * from the BulkWriteException. */
                $test->assertThat($actual, $test->logicalOr(
                    $test->isInstanceOf(BulkWriteResult::class),
                    $test->isInstanceOf(WriteResult::class)
                ));

                if (! $actual->isAcknowledged()) {
                    break;
                }

                if (isset($expected->deletedCount)) {
                    $test->assertSame($expected->deletedCount, $actual->getDeletedCount());
                }

                if (isset($expected->insertedCount)) {
                    $test->assertSame($expected->insertedCount, $actual->getInsertedCount());
                }

                // insertedIds are not available after BulkWriteException (see: PHPLIB-428)
                if (isset($expected->insertedIds) && $actual instanceof BulkWriteResult) {
                    $test->assertSameDocument($expected->insertedIds, $actual->getInsertedIds());
                }

                if (isset($expected->matchedCount)) {
                    $test->assertSame($expected->matchedCount, $actual->getMatchedCount());
                }

                if (isset($expected->modifiedCount)) {
                    $test->assertSame($expected->modifiedCount, $actual->getModifiedCount());
                }

                if (isset($expected->upsertedCount)) {
                    $test->assertSame($expected->upsertedCount, $actual->getUpsertedCount());
                }

                if (isset($expected->upsertedIds)) {
                    $test->assertSameDocument($expected->upsertedIds, $actual->getUpsertedIds());
                }
                break;

            case self::ASSERT_CALLABLE:
                call_user_func($this->assertionCallable, $expected, $actual);
                break;

            case self::ASSERT_DELETE:
                $test->assertInstanceOf(DeleteResult::class, $actual);

                if (isset($expected->deletedCount)) {
                    $test->assertSame($expected->deletedCount, $actual->getDeletedCount());
                }
                break;

            case self::ASSERT_INSERTMANY:
                /* If the bulk insert was successful, the actual value should be
                 * a InsertManyResult; otherwise, expect a WriteResult extracted
                 * from the BulkWriteException. */
                $test->assertThat($actual, $test->logicalOr(
                    $test->isInstanceOf(InsertManyResult::class),
                    $test->isInstanceOf(WriteResult::class)
                ));

                if (isset($expected->insertedCount)) {
                    $test->assertSame($expected->insertedCount, $actual->getInsertedCount());
                }

                // insertedIds are not available after BulkWriteException (see: PHPLIB-428)
                if (isset($expected->insertedIds) && $actual instanceof BulkWriteResult) {
                    $test->assertSameDocument($expected->insertedIds, $actual->getInsertedIds());
                }
                break;

            case self::ASSERT_INSERTONE:
                $test->assertThat($actual, $test->logicalOr(
                    $test->isInstanceOf(InsertOneResult::class),
                    $test->isInstanceOf(WriteResult::class)
                ));

                if (isset($expected->insertedCount)) {
                    $test->assertSame($expected->insertedCount, $actual->getInsertedCount());
                }

                if (property_exists($expected, 'insertedId')) {
                    $test->assertSameDocument(
                        ['insertedId' => $expected->insertedId],
                        ['insertedId' => $actual->getInsertedId()]
                    );
                }
                break;

            case self::ASSERT_MATCHES_DOCUMENT:
                $test->assertIsObject($expected);
                $test->assertThat($actual, $test->logicalOr(
                    $test->isType('array'),
                    $test->isType('object')
                ));
                $test->assertMatchesDocument($expected, $actual);
                break;

            case self::ASSERT_NOTHING:
                break;

            case self::ASSERT_NULL:
                $test->assertNull($actual);
                break;

            case self::ASSERT_SAME:
                $test->assertSame($expected, $actual);
                break;

            case self::ASSERT_SAME_DOCUMENT:
                $test->assertIsObject($expected);
                $test->assertThat($actual, $test->logicalOr(
                    $test->isType('array'),
                    $test->isType('object')
                ));
                $test->assertSameDocument($expected, $actual);
                break;

            case self::ASSERT_SAME_DOCUMENTS:
                $test->assertSameDocuments($expected, $actual);
                break;

            case self::ASSERT_DOCUMENTS_MATCH:
                $test->assertDocumentsMatch($expected, $actual);
                break;

            case self::ASSERT_UPDATE:
                $test->assertInstanceOf(UpdateResult::class, $actual);

                if (isset($expected->matchedCount)) {
                    $test->assertSame($expected->matchedCount, $actual->getMatchedCount());
                }

                if (isset($expected->modifiedCount)) {
                    $test->assertSame($expected->modifiedCount, $actual->getModifiedCount());
                }

                if (isset($expected->upsertedCount)) {
                    $test->assertSame($expected->upsertedCount, $actual->getUpsertedCount());
                }

                if (property_exists($expected, 'upsertedId')) {
                    $test->assertSameDocument(
                        ['upsertedId' => $expected->upsertedId],
                        ['upsertedId' => $actual->getUpsertedId()]
                    );
                }
                break;

            default:
                throw new LogicException('Unsupported assertion type: ' . $this->assertionType);
        }
    }

    public function isExpected()
    {
        return $this->assertionType !== self::ASSERT_NOTHING;
    }

    private static function isArrayOfObjects($array)
    {
        if (! is_array($array)) {
            return false;
        }

        foreach ($array as $object) {
            if (! is_object($object)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determines whether the result is actually an error expectation.
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/transactions/tests/README.rst#test-format
     * @param mixed $result
     * @return boolean
     */
    private static function isErrorResult($result)
    {
        if (! is_object($result)) {
            return false;
        }

        $keys = ['errorContains', 'errorCodeName', 'errorLabelsContain', 'errorLabelsOmit'];

        foreach ($keys as $key) {
            if (isset($result->{$key})) {
                return true;
            }
        }

        return false;
    }
}
