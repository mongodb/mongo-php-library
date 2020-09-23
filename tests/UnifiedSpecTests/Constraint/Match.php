<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use LogicException;
use MongoDB\BSON\Type;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\UnifiedSpecTests\EntityMap;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalOr;
use RuntimeException;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use Symfony\Bridge\PhpUnit\ConstraintTrait;
use function array_values;
use function count;
use function get_class;
use function get_resource_type;
use function gettype;
use function hex2bin;
use function implode;
use function is_array;
use function is_bool;
use function is_object;
use function is_resource;
use function is_string;
use function sprintf;
use function stream_get_contents;
use function strpos;
use const PHP_INT_SIZE;

/**
 * Constraint that checks if one document matches another.
 *
 * The expected value is passed in the constructor.
 */
class Match extends Constraint
{
    use ConstraintTrait;

    /** @var EntityMap */
    private $entityMap;

    /** @var boolean */
    private $ignoreExtraKeysInRoot = false;

    /** @var boolean */
    private $ignoreExtraKeysInEmbedded = false;

    /** @var mixed */
    private $value;

    /** @var ComparisonFailure|null */
    private $lastFailure;

    public function __construct($value, bool $ignoreExtraKeysInRoot = false, bool $ignoreExtraKeysInEmbedded = false, EntityMap $entityMap = null)
    {
        $this->value = self::prepare($value, true);
        $this->entityMap = $entityMap ?? new EntityMap();
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

            return;
        }

        if ($expected instanceof BSONArray) {
            if (! $actual instanceof BSONArray) {
                throw new RuntimeException(sprintf(
                    '%s%s is not instance of expected class "%s"',
                    (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                    $this->exporter()->shortenedExport($actual),
                    BSONArray::class
                ));
            }

            if (count($expected) !== count($actual)) {
                throw new RuntimeException(sprintf(
                    '%s%s has %d elements instead of %d expected',
                    (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                    $this->exporter()->shortenedExport($actual),
                    count($actual),
                    count($expected)
                ));
            }

            foreach ($expected as $key => $expectedValue) {
                $this->assertMatches(
                    $expectedValue,
                    $actual[$key],
                    $this->ignoreExtraKeysInEmbedded,
                    (empty($keyPath) ? $key : $keyPath . '.' . $key)
                );
            }

            return;
        }

        if ($expected instanceof BSONDocument) {
            if (self::isSpecialOperator($expected)) {
                $operator = self::getSpecialOperator($expected);

                // TODO: Validate structure of operators
                if ($operator === '$$type') {
                    $types = is_string($expected['$$type']) ? [$expected['$$type']] : $expected['$$type'];
                    $constraints = [];

                    foreach ($types as $type) {
                        $constraints[] = new IsBsonType($type);
                    }

                    $constraint = LogicalOr::fromConstraints(...$constraints);

                    if (! $constraint->evaluate($actual, '', true)) {
                        throw new RuntimeException(sprintf(
                            '%s%s is not an expected type: %s',
                            (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                            $this->exporter()->shortenedExport($actual),
                            implode(', ', $types)
                        ));
                    }

                    return;
                }

                if ($operator === '$$matchesEntity') {
                    $entityMap = $this->getEntityMap();

                    $this->assertMatches(
                        $entityMap[$expected['$$matchesEntity']],
                        $actual,
                        $ignoreExtraKeys,
                        $keyPath
                    );

                    return;
                }

                if ($operator === '$$matchesHexBytes') {
                    if (! is_resource($actual) || get_resource_type($actual) != "stream") {
                        throw new RuntimeException(sprintf(
                            '%s%s is not a stream',
                            (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                            $this->exporter()->shortenedExport($actual),
                        ));
                    }

                    if (stream_get_contents($actual, -1, 0) !== hex2bin($expected['$$matchesHexBytes'])) {
                        throw new RuntimeException(sprintf(
                            '%s%s does not match expected hex bytes: %s',
                            (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                            $this->exporter()->shortenedExport($actual),
                            $expected['$$matchesHexBytes']
                        ));
                    }

                    return;
                }

                if ($operator === '$$unsetOrMatches') {
                    /* If the operator is used at the top level, consider null
                     * values for $actual to be unset. If the operator is nested
                     * this check is done later document iteration. */
                    if ($keyPath === '' && $actual === null) {
                        return;
                    }

                    $this->assertMatches(
                        $expected['$$unsetOrMatches'],
                        $actual,
                        $ignoreExtraKeys,
                        $keyPath
                    );

                    return;
                }

                if ($operator === '$$sessionLsid') {
                    $entityMap = $this->getEntityMap();
                    $session = $entityMap['$$sessionLsid'];

                    if (! $session instanceof Session) {
                        throw new RuntimeException(sprintf(
                            '%sentity "%s" is not a session',
                            (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                            $entityMap['$$sessionLsid'],
                        ));
                    }

                    $this->assertMatches(
                        $this->prepare($session->getLogicalSessionId(), true),
                        $actual,
                        false, /* LSID document should match exactly */
                        $keyPath
                    );
                }

                throw new LogicException('unsupported operator: ' . $operator);
            }

            if (! $actual instanceof BSONDocument) {
                throw new RuntimeException(sprintf(
                    '%s%s is not instance of expected class "%s"',
                    (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                    $this->exporter()->shortenedExport($actual),
                    BSONDocument::class
                ));
            }

            foreach ($expected as $key => $expectedValue) {
                $actualKeyExists = $actual->offsetExists($key);

                if ($expectedValue instanceof BSONDocument && self::isSpecialOperator($expectedValue)) {
                    $operator = self::getSpecialOperator($expectedValue);

                    if ($operator === '$$exists') {
                        if (! is_bool($expectedValue['$$exists'])) {
                            throw new RuntimeException('$$exists is malformed');
                        }

                        if ($expectedValue['$$exists'] && ! $actualKeyExists) {
                            throw new RuntimeException(sprintf(
                                '%s%s does not have expected key "%s"',
                                (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                                $this->exporter()->shortenedExport($actual),
                                $key
                            ));
                        }

                        if (! $expectedValue['$$exists'] && $actualKeyExists) {
                            throw new RuntimeException(sprintf(
                                '%s%s has unexpected key "%s"',
                                (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                                $this->exporter()->shortenedExport($actual),
                                $key
                            ));
                        }

                        continue;
                    }

                    if ($operator === '$$unsetOrMatches' && ! $actualKeyExists) {
                        continue;
                    }
                }

                if (! $actualKeyExists) {
                    throw new RuntimeException(sprintf(
                        '%s$actual does not have expected key "%s"',
                        (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                        $key
                    ));
                }

                $this->assertMatches(
                    $expectedValue,
                    $actual[$key],
                    $this->ignoreExtraKeysInEmbedded,
                    (empty($keyPath) ? $key : $keyPath . '.' . $key)
                );
            }

            if ($ignoreExtraKeys) {
                return;
            }

            foreach ($actual as $key => $_) {
                if (! $expected->offsetExists($key)) {
                    throw new RuntimeException(sprintf(
                        '%s$actual has extra key "%s"',
                        (empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath)),
                        $key
                    ));
                }
            }

            return;
        }

        throw new LogicException('should not reach this point');
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

    private static function getSpecialOperator(BSONDocument $document) : string
    {
        foreach ($document as $key => $_) {
            if (strpos((string) $key, '$$') === 0) {
                return $key;
            }
        }

        throw new LogicException('should not reach this point');
    }

    private static function isSpecialOperator(BSONDocument $document) : bool
    {
        if (count($document) !== 1) {
            return false;
        }

        foreach ($document as $key => $_) {
            return strpos((string) $key, '$$') === 0;
        }

        throw new LogicException('should not reach this point');
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
