<?php

namespace MongoDB\Tests\SpecTests;

use ArrayObject;
use InvalidArgumentException;
use MongoDB\BSON\Int64;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\UnifiedSpecTests\Constraint\IsBsonType;
use PHPUnit\Framework\Constraint\Constraint;
use RuntimeException;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use stdClass;
use Symfony\Bridge\PhpUnit\ConstraintTrait;

use function array_values;
use function get_class;
use function get_debug_type;
use function is_array;
use function is_float;
use function is_int;
use function is_object;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\containsOnly;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\isType;
use function PHPUnit\Framework\logicalAnd;
use function PHPUnit\Framework\logicalOr;
use function sprintf;

use const PHP_INT_SIZE;

/**
 * Constraint that checks if one document matches another.
 *
 * The expected value is passed in the constructor.
 */
class DocumentsMatchConstraint extends Constraint
{
    use ConstraintTrait;

    /** @var boolean */
    private $ignoreExtraKeysInRoot = false;

    /** @var boolean */
    private $ignoreExtraKeysInEmbedded = false;

    /**
     * TODO: This is not currently used, but was preserved from the design of
     * TestCase::assertMatchesDocument(), which would sort keys and then compare
     * documents as JSON strings. If the TODO item in matches() is implemented
     * to make document comparisons more efficient, we may consider supporting
     * this option.
     *
     * @var boolean
     */
    private $sortKeys = false;

    /** @var BSONArray|BSONDocument */
    private $value;

    /** @var ComparisonFailure|null */
    private $lastFailure;

    /** @var Factory */
    private $comparatorFactory;

    /**
     * Creates a new constraint.
     *
     * @param array|object $value
     * @param boolean      $ignoreExtraKeysInRoot     If true, ignore extra keys within the root document
     * @param boolean      $ignoreExtraKeysInEmbedded If true, ignore extra keys within embedded documents
     */
    public function __construct($value, bool $ignoreExtraKeysInRoot = false, bool $ignoreExtraKeysInEmbedded = false)
    {
        $this->value = $this->prepareBSON($value, true, $this->sortKeys);
        $this->ignoreExtraKeysInRoot = $ignoreExtraKeysInRoot;
        $this->ignoreExtraKeysInEmbedded = $ignoreExtraKeysInEmbedded;
        $this->comparatorFactory = Factory::getInstance();
    }

    private function doEvaluate($other, $description = '', $returnResult = false)
    {
        /* TODO: If ignoreExtraKeys and sortKeys are both false, then we may be
         * able to skip preparation, convert both documents to extended JSON,
         * and compare strings.
         *
         * If ignoreExtraKeys is false and sortKeys is true, we still be able to
         * compare JSON strings but will still require preparation to sort keys
         * in all documents and sub-documents. */
        $other = $this->prepareBSON($other, true, $this->sortKeys);

        $success = false;
        $this->lastFailure = null;

        try {
            $this->assertEquals($this->value, $other, $this->ignoreExtraKeysInRoot);
            $success = true;
        } catch (RuntimeException $e) {
            $this->lastFailure = new ComparisonFailure(
                $this->value,
                $other,
                $this->exporter()->export($this->value),
                $this->exporter()->export($other),
                false,
                $e->getMessage()
            );
        }

        if ($returnResult) {
            return $success;
        }

        if (! $success) {
            $this->fail($other, $description, $this->lastFailure);
        }
    }

    /**
     * @param string|string[] $expectedType
     * @param mixed           $actualValue
     */
    private function assertBSONType($expectedType, $actualValue): void
    {
        assertThat(
            $expectedType,
            logicalOr(isType('string'), logicalAnd(isInstanceOf(BSONArray::class), containsOnly('string'))),
            '$$type requires string or string[]'
        );

        IsBsonType::anyOf(...(array) $expectedType)->evaluate($actualValue);
    }

    /**
     * Compares two documents recursively.
     *
     * @throws RuntimeException if the documents do not match
     */
    private function assertEquals(ArrayObject $expected, ArrayObject $actual, bool $ignoreExtraKeys, string $keyPrefix = ''): void
    {
        if (get_class($expected) !== get_class($actual)) {
            throw new RuntimeException(sprintf(
                '%s is not instance of expected class "%s"',
                $this->exporter()->shortenedExport($actual),
                get_class($expected)
            ));
        }

        foreach ($expected as $key => $expectedValue) {
            $actualHasKey = $actual->offsetExists($key);

            if (! $actualHasKey) {
                throw new RuntimeException(sprintf('$actual is missing key: "%s"', $keyPrefix . $key));
            }

            $actualValue = $actual[$key];

            if ($expectedValue instanceof BSONDocument && isset($expectedValue['$$type'])) {
                $this->assertBSONType($expectedValue['$$type'], $actualValue);
                continue;
            }

            if (
                ($expectedValue instanceof BSONArray && $actualValue instanceof BSONArray) ||
                ($expectedValue instanceof BSONDocument && $actualValue instanceof BSONDocument)
            ) {
                $this->assertEquals($expectedValue, $actualValue, $this->ignoreExtraKeysInEmbedded, $keyPrefix . $key . '.');
                continue;
            }

            $expectedType = get_debug_type($expectedValue);
            $actualType = get_debug_type($actualValue);

            /* Early check to work around ObjectComparator printing the entire value
             * for a failed type comparison. Avoid doing this if either value is
             * numeric to allow for flexible numeric comparisons (e.g. 1 == 1.0). */
            if ($expectedType !== $actualType && ! (self::isNumeric($expectedValue) || self::isNumeric($actualValue))) {
                throw new ComparisonFailure(
                    $expectedValue,
                    $actualValue,
                    '',
                    '',
                    false,
                    sprintf(
                        'Field path "%s": %s is not instance of expected type "%s".',
                        $keyPrefix . $key,
                        $this->exporter()->shortenedExport($actualValue),
                        $expectedType
                    )
                );
            }

            try {
                $this->comparatorFactory->getComparatorFor($expectedValue, $actualValue)->assertEquals($expectedValue, $actualValue);
            } catch (ComparisonFailure $failure) {
                throw new ComparisonFailure(
                    $expectedValue,
                    $actualValue,
                    '',
                    '',
                    false,
                    sprintf('Field path "%s": %s', $keyPrefix . $key, $failure->getMessage())
                );
            }
        }

        if ($ignoreExtraKeys) {
            return;
        }

        foreach ($actual as $key => $value) {
            if (! $expected->offsetExists($key)) {
                throw new RuntimeException(sprintf('$actual has extra key: "%s"', $keyPrefix . $key));
            }
        }
    }

    private function doAdditionalFailureDescription($other)
    {
        if ($this->lastFailure === null) {
            return '';
        }

        return $this->lastFailure->getMessage();
    }

    private function doFailureDescription($other)
    {
        return 'two BSON objects are equal';
    }

    private function doMatches($other)
    {
        /* TODO: If ignoreExtraKeys and sortKeys are both false, then we may be
         * able to skip preparation, convert both documents to extended JSON,
         * and compare strings.
         *
         * If ignoreExtraKeys is false and sortKeys is true, we still be able to
         * compare JSON strings but will still require preparation to sort keys
         * in all documents and sub-documents. */
        $other = $this->prepareBSON($other, true, $this->sortKeys);

        try {
            $this->assertEquals($this->value, $other, $this->ignoreExtraKeysInRoot);
        } catch (RuntimeException $e) {
            return false;
        }

        return true;
    }

    private function doToString()
    {
        return 'matches ' . $this->exporter()->export($this->value);
    }

    private static function isNumeric($value): bool
    {
        return is_int($value) || is_float($value) || $value instanceof Int64;
    }

    /**
     * Prepare a BSON document or array for comparison.
     *
     * The argument will be converted to a BSONArray or BSONDocument based on
     * its type and keys. Keys within documents will optionally be sorted. Each
     * value within the array or document will then be prepared recursively.
     *
     * @param array|object $bson
     * @param boolean      $isRoot If true, ensure an array value is converted to a document
     * @return BSONDocument|BSONArray
     * @throws InvalidArgumentException if $bson is not an array or object
     */
    private function prepareBSON($bson, bool $isRoot, bool $sortKeys = false)
    {
        if (! is_array($bson) && ! is_object($bson)) {
            throw new InvalidArgumentException('$bson is not an array or object');
        }

        if ($isRoot && is_array($bson)) {
            $bson = (object) $bson;
        }

        if ($bson instanceof BSONArray || (is_array($bson) && $bson === array_values($bson))) {
            if (! $bson instanceof BSONArray) {
                $bson = new BSONArray($bson);
            }
        } else {
            if (! $bson instanceof BSONDocument) {
                $bson = new BSONDocument((array) $bson);
            }

            if ($sortKeys) {
                $bson->ksort();
            }
        }

        foreach ($bson as $key => $value) {
            if ($value instanceof BSONArray || (is_array($value) && $value === array_values($value))) {
                $bson[$key] = $this->prepareBSON($value, false, $sortKeys);
                continue;
            }

            if ($value instanceof BSONDocument || $value instanceof stdClass || is_array($value)) {
                $bson[$key] = $this->prepareBSON($value, false, $sortKeys);
                continue;
            }

            /* Convert Int64 objects to integers on 64-bit platforms for
             * compatibility reasons. */
            if ($value instanceof Int64 && PHP_INT_SIZE != 4) {
                $bson[$key] = (int) ((string) $value);
            }
        }

        return $bson;
    }
}
