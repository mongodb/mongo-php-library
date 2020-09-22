<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use ArrayObject;
use InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use RuntimeException;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use stdClass;
use Symfony\Bridge\PhpUnit\ConstraintTrait;
use function array_values;
use function get_class;
use function gettype;
use function in_array;
use function is_array;
use function is_object;
use function is_scalar;
use function method_exists;
use function sprintf;
use const PHP_INT_SIZE;

/**
 * Constraint that checks if one document matches another.
 *
 * The expected value is passed in the constructor.
 */
class Match extends Constraint
{
    use ConstraintTrait;

    /** @var boolean */
    private $ignoreExtraKeysInRoot = false;

    /** @var boolean */
    private $ignoreExtraKeysInEmbedded = false;

    /** @var mixed */
    private $value;

    /** @var ComparisonFailure|null */
    private $lastFailure;

    public function __construct($value, $ignoreExtraKeysInRoot = false, $ignoreExtraKeysInEmbedded = false)
    {
        $this->value = self::prepare($value, true);
        $this->ignoreExtraKeysInRoot = $ignoreExtraKeysInRoot;
        $this->ignoreExtraKeysInEmbedded = $ignoreExtraKeysInEmbedded;
        $this->comparatorFactory = Factory::getInstance();
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        $other = self::prepare($other, true);
        $success = false;
        $this->lastFailure = null;

        try {
            $this->assertMatches($this->value, $other, $this->ignoreExtraKeysInRoot);
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

    private function assertEquals($expected, $actual, string $keyPath)
    {
        $expectedType = is_object($expected) ? get_class($expected) : gettype($expected);
        $actualType = is_object($actual) ? get_class($actual) : gettype($actual);

        // Workaround for ObjectComparator printing the whole actual object
        if ($expectedType !== $actualType) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                '',
                '',
                false,
                sprintf(
                    '%s%s is not instance of expected type "%s".',
                    empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath),
                    $this->exporter()->shortenedExport($actual),
                    $expectedType
                )
            );
        }

        try {
            $this->comparatorFactory->getComparatorFor($expected, $actual)->assertEquals($expected, $actual);
        } catch (ComparisonFailure $failure) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                '',
                '',
                false,
                (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)) . $failure->getMessage()
            );
        }
    }

    /**
     * Compares two BSON values recursively.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param bool   $ignoreExtraKeys
     * @param string $keyPath
     * @throws RuntimeException if the documents do not match
     */
    private function assertMatches($expected, $actual, bool $ignoreExtraKeys, $keyPath = '')
    {
        if (! $expected instanceof BSONDocument && ! $expected instanceof BSONArray) {
            $this->assertEquals($expected, $actual, $keyPath);
        }

        if ($expected instanceof BSONArray) {

        }

        if ($expected instanceof BSONDocument) {
            if (self::isSpecialOperator($expected)) {

            }
        }


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

            if ($expectedValue instanceof BSONDocument && isset($expectedValue['$$type'])) {
                $this->assertBSONType($expectedValue['$$type'], $actualValue);
                continue;
            }

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
        return 'expected value matches actual value';
    }

    private function doMatches($other)
    {
        $other = self::prepare($other, true);

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

    private static function isSpecialOperator(BSONDocument $document): bool
    {
        foreach ($document as $key => $_) {
            return strpos((string) $key, '$$') === 0;
        }

        return false;
    }

    /**
     * Prepare a value for comparison.
     *
     * If the value is an array or object, it will be converted to a BSONArray
     * or BSONDocument. If $value is an array and $isRoot is true, it will be
     * converted to a BSONDocument; otherwise, it will be converted to a
     * BSONArray or BSONDocument based on its keys. Each value within an array
     * or document will then be prepared recursively.
     *
     * @param mixed   $bson
     * @param boolean $isRoot If true, convert an array to a BSONDocument
     */
    private static function prepare($bson, bool $isRoot)
    {
        if (! is_array($bson) && ! is_object($bson)) {
            return $bson;
        }

        /* Convert Int64 objects to integers on 64-bit platforms for
         * compatibility reasons. */
        if ($bson instanceof Int64 && PHP_INT_SIZE != 4) {
            return (int) ((string) $bson);
        }

        // TODO: ignore Serializable if needed
        if ($bson instanceof Type) {
            return $bson;
        }

        if ($isRoot && is_array($bson)) {
            $bson = new BSONDocument($bson);
        }

        if (is_array($bson) && $bson === array_values($bson)) {
            $bson = new BSONArray($bson);
        }

        if (! $bson instanceof BSONArray && ! $bson instanceof BSONDocument) {
            // TODO: determine if (array) cast is needed
            $bson = new BSONDocument($bson);
        }

        foreach ($bson as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $bson[$key] = self::prepare($value, false);
            }
        }

        return $bson;
    }
}
