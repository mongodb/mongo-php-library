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
use SebastianBergmann\Exporter\Exporter;
use stdClass;

use function array_values;
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

/**
 * Constraint that checks if one document matches another.
 *
 * The expected value is passed in the constructor.
 */
class DocumentsMatchConstraint extends Constraint
{
    /**
     * TODO: This is not currently used, but was preserved from the design of
     * TestCase::assertMatchesDocument(), which would sort keys and then compare
     * documents as JSON strings. If the TODO item in matches() is implemented
     * to make document comparisons more efficient, we may consider supporting
     * this option.
     */
    private bool $sortKeys = false;

    private BSONArray|BSONDocument $value;

    private ?ComparisonFailure $lastFailure = null;

    private Factory $comparatorFactory;

    /**
     * Creates a new constraint.
     *
     * @param boolean $ignoreExtraKeysInRoot     If true, ignore extra keys within the root document
     * @param boolean $ignoreExtraKeysInEmbedded If true, ignore extra keys within embedded documents
     */
    public function __construct(array|object $value, private bool $ignoreExtraKeysInRoot = false, private bool $ignoreExtraKeysInEmbedded = false)
    {
        $this->value = $this->prepareBSON($value, true, $this->sortKeys);
        $this->comparatorFactory = Factory::getInstance();
    }

    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
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
            $exporter = new Exporter();
            $this->lastFailure = new ComparisonFailure(
                $this->value,
                $other,
                $exporter->export($this->value),
                $exporter->export($other),
                $e->getMessage(),
            );
        }

        if ($returnResult) {
            return $success;
        }

        if (! $success) {
            $this->fail($other, $description, $this->lastFailure);
        }

        return null;
    }

    /** @param string|BSONArray[] $expectedType */
    private function assertBSONType(string|BSONArray $expectedType, mixed $actualValue): void
    {
        assertThat(
            $expectedType,
            logicalOr(isType('string'), logicalAnd(isInstanceOf(BSONArray::class), containsOnly('string'))),
            '$$type requires string or string[]',
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
        if ($expected::class !== $actual::class) {
            throw new RuntimeException(sprintf(
                '%s is not instance of expected class "%s"',
                (new Exporter())->shortenedExport($actual),
                $expected::class,
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
                    sprintf(
                        'Field path "%s": %s is not instance of expected type "%s".',
                        $keyPrefix . $key,
                        (new Exporter())->shortenedExport($actualValue),
                        $expectedType,
                    ),
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
                    sprintf('Field path "%s": %s', $keyPrefix . $key, $failure->getMessage()),
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

    protected function additionalFailureDescription($other): string
    {
        if ($this->lastFailure === null) {
            return '';
        }

        return $this->lastFailure->getMessage();
    }

    protected function failureDescription($other): string
    {
        return 'two BSON objects are equal';
    }

    protected function matches($other): bool
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
        } catch (RuntimeException) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return 'matches ' . (new Exporter())->export($this->value);
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
     * @param boolean $isRoot If true, ensure an array value is converted to a document
     * @throws InvalidArgumentException if $bson is not an array or object
     */
    private function prepareBSON(array|object $bson, bool $isRoot, bool $sortKeys = false): BSONDocument|BSONArray
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
        }

        return $bson;
    }
}
