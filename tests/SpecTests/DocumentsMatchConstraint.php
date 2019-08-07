<?php

namespace MongoDB\Tests\SpecTests;

use ArrayObject;
use InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Constraint\Constraint;
use RuntimeException;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use stdClass;
use Symfony\Bridge\PhpUnit\ConstraintTrait;
use function array_values;
use function get_class;
use function in_array;
use function is_array;
use function is_object;
use function is_scalar;
use function sprintf;

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

    /** @var array */
    private $placeholders = [];

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
     * @param array        $placeholders              Placeholders for any value
     */
    public function __construct($value, $ignoreExtraKeysInRoot = false, $ignoreExtraKeysInEmbedded = false, array $placeholders = [])
    {
        $this->value = $this->prepareBSON($value, true, $this->sortKeys);
        $this->ignoreExtraKeysInRoot = $ignoreExtraKeysInRoot;
        $this->ignoreExtraKeysInEmbedded = $ignoreExtraKeysInEmbedded;
        $this->placeholders = $placeholders;
        $this->comparatorFactory = Factory::getInstance();
    }

    public function evaluate($other, $description = '', $returnResult = false)
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
     * Compares two documents recursively.
     *
     * @param ArrayObject $expected
     * @param ArrayObject $actual
     * @param boolean     $ignoreExtraKeys
     * @param string      $keyPrefix
     * @throws RuntimeException if the documents do not match
     */
    private function assertEquals(ArrayObject $expected, ArrayObject $actual, $ignoreExtraKeys, $keyPrefix = '')
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

            if (in_array($expectedValue, $this->placeholders, true)) {
                continue;
            }

            $actualValue = $actual[$key];

            if (($expectedValue instanceof BSONArray && $actualValue instanceof BSONArray) ||
                ($expectedValue instanceof BSONDocument && $actualValue instanceof BSONDocument)) {
                $this->assertEquals($expectedValue, $actualValue, $this->ignoreExtraKeysInEmbedded, $keyPrefix . $key . '.');
                continue;
            }

            if (is_scalar($expectedValue) && is_scalar($actualValue)) {
                if ($expectedValue !== $actualValue) {
                    throw new ComparisonFailure(
                        $expectedValue,
                        $actualValue,
                        '',
                        '',
                        false,
                        sprintf('Field path "%s": %s', $keyPrefix . $key, 'Failed asserting that two values are equal.')
                    );
                }

                continue;
            }

            // Workaround for ObjectComparator printing the whole actual object
            if (get_class($expectedValue) !== get_class($actualValue)) {
                throw new ComparisonFailure(
                    $expectedValue,
                    $actualValue,
                    '',
                    '',
                    false,
                    sprintf(
                        'Field path "%s": %s is not instance of expected class "%s".',
                        $keyPrefix . $key,
                        $this->exporter()->shortenedExport($actualValue),
                        get_class($expectedValue)
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

    /**
     * Prepare a BSON document or array for comparison.
     *
     * The argument will be converted to a BSONArray or BSONDocument based on
     * its type and keys. Keys within documents will optionally be sorted. Each
     * value within the array or document will then be prepared recursively.
     *
     * @param array|object $bson
     * @param boolean      $isRoot   If true, ensure an array value is converted to a document
     * @param boolean      $sortKeys
     * @return BSONDocument|BSONArray
     * @throws InvalidArgumentException if $bson is not an array or object
     */
    private function prepareBSON($bson, $isRoot, $sortKeys = false)
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
