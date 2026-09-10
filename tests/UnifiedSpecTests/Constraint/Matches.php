<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use LogicException;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\UnifiedSpecTests\EntityMap;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use SebastianBergmann\Exporter\Exporter;

use function array_keys;
use function count;
use function get_debug_type;
use function hex2bin;
use function implode;
use function is_array;
use function is_float;
use function is_int;
use function is_object;
use function ltrim;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsBool;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertLessThanOrEqual;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertStringStartsWith;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\containsOnly;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\isType;
use function PHPUnit\Framework\logicalAnd;
use function PHPUnit\Framework\logicalOr;
use function range;
use function sprintf;
use function str_starts_with;
use function strrchr;
use function trim;

/**
 * Constraint that checks if one value matches another.
 *
 * The expected value is passed in the constructor. An EntityMap may be supplied
 * for resolving operators (e.g. $$matchesEntity). Behavior for allowing extra
 * keys in root documents and processing operators is also configurable.
 */
class Matches extends Constraint
{
    private mixed $value;

    private bool $allowExtraRootKeys;

    private bool $allowOperators;

    private ?ComparisonFailure $lastFailure = null;

    private Factory $comparatorFactory;

    public function __construct($value, private ?EntityMap $entityMap = null, $allowExtraRootKeys = true, $allowOperators = true)
    {
        $this->value = self::prepare($value);
        $this->allowExtraRootKeys = $allowExtraRootKeys;
        $this->allowOperators = $allowOperators;
        $this->comparatorFactory = Factory::getInstance();
    }

    public function evaluate($other, $description = '', $returnResult = false): ?bool
    {
        $other = self::prepare($other);
        $success = false;
        $this->lastFailure = null;

        try {
            $this->assertMatches($this->value, $other);
            $success = true;
        } catch (ExpectationFailedException $e) {
            /* Rethrow internal assertion failures (e.g. operator type checks,
             * EntityMap errors), which are logical errors in the code/test. */
            throw $e;
        } catch (RuntimeException $e) {
            /* This will generally catch internal errors from failAt(), which
             * include a key path to pinpoint the failure. */
            $exporter = new Exporter();
            $this->lastFailure = new ComparisonFailure(
                $this->value,
                $other,
                /* TODO: Improve the exporter to canonicalize documents by
                 * sorting keys and remove spl_object_hash from output. */
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

    private function assertEquals($expected, $actual, string $keyPath): void
    {
        $expectedType = get_debug_type($expected);
        $actualType = get_debug_type($actual);

        /* Early check to work around ObjectComparator printing the entire value
         * for a failed type comparison. Avoid doing this if either value is
         * numeric to allow for flexible numeric comparisons (e.g. 1 == 1.0). */
        if ($expectedType !== $actualType && ! (self::isNumeric($expected) || self::isNumeric($actual))) {
            self::failAt(sprintf('%s is not expected type "%s"', $actualType, $expectedType), $keyPath);
        }

        try {
            $this->comparatorFactory->getComparatorFor($expected, $actual)->assertEquals($expected, $actual);
        } catch (ComparisonFailure $e) {
            /* Disregard other ComparisonFailure fields, as evaluate() only uses
             * the message when creating its own ComparisonFailure. */
            self::failAt($e->getMessage(), $keyPath);
        }
    }

    private function assertMatches($expected, $actual, string $keyPath = ''): void
    {
        if ($expected instanceof BSONArray) {
            $this->assertMatchesArray($expected, $actual, $keyPath);

            return;
        }

        if ($expected instanceof BSONDocument) {
            $this->assertMatchesDocument($expected, $actual, $keyPath);

            return;
        }

        $this->assertEquals($expected, $actual, $keyPath);
    }

    private function assertMatchesArray(BSONArray $expected, $actual, string $keyPath): void
    {
        if (! $actual instanceof BSONArray) {
            $actualType = get_debug_type($actual);
            self::failAt(sprintf('%s is not instance of expected class "%s"', $actualType, BSONArray::class), $keyPath);
        }

        if (count($expected) !== count($actual)) {
            self::failAt(sprintf('$actual count is %d, expected %d', count($actual), count($expected)), $keyPath);
        }

        foreach ($expected as $key => $expectedValue) {
            $this->assertMatches(
                $expectedValue,
                $actual[$key],
                (empty($keyPath) ? $key : $keyPath . '.' . $key),
            );
        }
    }

    private function assertMatchesDocument(BSONDocument $expected, $actual, string $keyPath): void
    {
        if ($this->allowOperators && self::isOperator($expected)) {
            $this->assertMatchesOperator($expected, $actual, $keyPath);

            return;
        }

        if (! $actual instanceof BSONDocument) {
            $actualType = get_debug_type($actual);
            self::failAt(sprintf('%s is not instance of expected class "%s"', $actualType, BSONDocument::class), $keyPath);
        }

        foreach ($expected as $key => $expectedValue) {
            $actualKeyExists = $actual->offsetExists($key);

            if ($this->allowOperators && $expectedValue instanceof BSONDocument && self::isOperator($expectedValue)) {
                $operatorName = self::getOperatorName($expectedValue);

                if ($operatorName === '$$exists') {
                    assertIsBool($expectedValue['$$exists'], '$$exists requires bool');

                    if ($expectedValue['$$exists'] && ! $actualKeyExists) {
                        self::failAt(sprintf('$actual does not have expected key "%s"', $key), $keyPath);
                    }

                    if (! $expectedValue['$$exists'] && $actualKeyExists) {
                        self::failAt(sprintf('$actual has unexpected key "%s"', $key), $keyPath);
                    }

                    continue;
                }

                if ($operatorName === '$$unsetOrMatches') {
                    if (! $actualKeyExists) {
                        continue;
                    }

                    $expectedValue = $expectedValue['$$unsetOrMatches'];
                }
            }

            if (! $actualKeyExists) {
                self::failAt(sprintf('$actual does not have expected key "%s"', $key), $keyPath);
            }

            $this->assertMatches(
                $expectedValue,
                $actual[$key],
                (empty($keyPath) ? $key : $keyPath . '.' . $key),
            );
        }

        // Ignore extra keys in root documents
        if ($this->allowExtraRootKeys && empty($keyPath)) {
            return;
        }

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        foreach ($actual as $key => $_) {
            if (! $expected->offsetExists($key)) {
                self::failAt(sprintf('$actual has unexpected key "%s"', $key), $keyPath);
            }
        }
    }

    private function assertMatchesOperator(BSONDocument $operator, $actual, string $keyPath): void
    {
        $name = self::getOperatorName($operator);

        if ($name === '$$exists') {
            assertIsBool($operator['$$exists'], '$$exists requires bool');

            /* If we get to this point, the field itself must already exist so
             * we need only fail if $$exists is false. */
            if ($operator['$$exists'] === false) {
                $key = ltrim(strrchr($keyPath, '.'), '.');
                self::failAt(sprintf('$actual has unexpected key "%s"', $key), $keyPath);
            }

            return;
        }

        if ($name === '$$type') {
            assertThat(
                $operator['$$type'],
                logicalOr(isType('string'), logicalAnd(isInstanceOf(BSONArray::class), containsOnly('string'))),
                '$$type requires string or string[]',
            );

            $constraint = IsBsonType::anyOf(...(array) $operator['$$type']);

            if (! $constraint->evaluate($actual, '', true)) {
                self::failAt(sprintf('%s is not an expected BSON type: %s', (new Exporter())->shortenedExport($actual), implode(', ', (array) $operator['$$type'])), $keyPath);
            }

            return;
        }

        if ($name === '$$matchAsDocument') {
            assertInstanceOf(BSONDocument::class, $operator['$$matchAsDocument'], '$$matchAsDocument requires a BSON document');
            assertIsString($actual, '$$matchAsDocument requires actual value to be a JSON string');
            assertJson($actual, '$$matchAsDocument requires actual value to be a JSON string');

            /* Note: assertJson() accepts array and scalar values, but the spec
             * assumes that the JSON string will yield a document. */
            assertStringStartsWith('{', trim($actual), '$$matchAsDocument requires actual value to be a JSON string denoting an object');

            $actualDocument = Document::fromJSON($actual)->toPHP();
            $constraint = new Matches($operator['$$matchAsDocument'], $this->entityMap, allowExtraRootKeys: false);

            if (! $constraint->evaluate($actualDocument, '', true)) {
                self::failAt(sprintf('%s did not match: %s', (new Exporter())->shortenedExport($actual), $constraint->additionalFailureDescription(null)), $keyPath);
            }

            return;
        }

        if ($name === '$$matchAsRoot') {
            $constraint = new Matches($operator['$$matchAsRoot'], $this->entityMap, allowExtraRootKeys: true);

            if (! $constraint->evaluate($actual, '', true)) {
                self::failAt(sprintf('$actual did not match as root-level document: %s', $constraint->additionalFailureDescription(null)), $keyPath);
            }

            return;
        }

        if ($name === '$$matchesEntity') {
            assertNotNull($this->entityMap, '$$matchesEntity requires EntityMap');
            assertIsString($operator['$$matchesEntity'], '$$matchesEntity requires string');

            /* TODO: Consider including the entity ID in any error message to
             * assist with diagnosing errors. Also consider disabling operators
             * within this match, since entities are unlikely to use them. */
            $this->assertMatches(
                self::prepare($this->entityMap[$operator['$$matchesEntity']]),
                $actual,
                $keyPath,
            );

            return;
        }

        if ($name === '$$matchesHexBytes') {
            assertIsString($operator['$$matchesHexBytes'], '$$matchesHexBytes requires string');
            assertMatchesRegularExpression('/^([0-9a-fA-F]{2})*$/', $operator['$$matchesHexBytes'], '$$matchesHexBytes requires pairs of hex chars');
            assertIsString($actual);

            if ($actual !== hex2bin($operator['$$matchesHexBytes'])) {
                self::failAt(sprintf('%s does not match expected hex bytes: %s', (new Exporter())->shortenedExport($actual), $operator['$$matchesHexBytes']), $keyPath);
            }

            return;
        }

        if ($name === '$$unsetOrMatches') {
            /* If the operator is used at the top level, consider null values
             * for $actual to be unset. If the operator is nested, this check is
             * done later during document iteration. */
            if ($keyPath === '' && $actual === null) {
                return;
            }

            $this->assertMatches(
                self::prepare($operator['$$unsetOrMatches']),
                $actual,
                $keyPath,
            );

            return;
        }

        if ($name === '$$sessionLsid') {
            assertNotNull($this->entityMap, '$$sessionLsid requires EntityMap');
            assertIsString($operator['$$sessionLsid'], '$$sessionLsid requires string');
            $lsid = $this->entityMap->getLogicalSessionId($operator['$$sessionLsid']);

            $this->assertEquals(self::prepare($lsid), $actual, $keyPath);

            return;
        }

        if ($name === '$$lte') {
            assertTrue(self::isNumeric($operator['$$lte']), '$$lte requires number');
            assertTrue(self::isNumeric($actual), '$actual operand for $$lte should be a number');
            assertLessThanOrEqual($operator['$$lte'], $actual);

            return;
        }

        throw new LogicException('unsupported operator: ' . $name);
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
        return 'expected value matches actual value';
    }

    protected function matches($other): bool
    {
        $other = self::prepare($other);

        try {
            $this->assertMatches($this->value, $other);
        } catch (RuntimeException) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return 'matches ' . (new Exporter())->export($this->value);
    }

    /** @psalm-return never-return */
    private static function failAt(string $message, string $keyPath): void
    {
        $prefix = empty($keyPath) ? '' : sprintf('Field path "%s": ', $keyPath);

        throw new RuntimeException($prefix . $message);
    }

    private static function getOperatorName(BSONDocument $document): string
    {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        foreach ($document as $key => $_) {
            if (str_starts_with((string) $key, '$$')) {
                return $key;
            }
        }

        throw new LogicException('should not reach this point');
    }

    private static function isNumeric($value)
    {
        return is_int($value) || is_float($value) || $value instanceof Int64;
    }

    private static function isOperator(BSONDocument $document): bool
    {
        if (count($document) !== 1) {
            return false;
        }

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        foreach ($document as $key => $_) {
            return str_starts_with((string) $key, '$$');
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
     */
    private static function prepare(mixed $bson): mixed
    {
        if (! is_array($bson) && ! is_object($bson)) {
            return $bson;
        }

        // Serializable can produce an array or object, so recurse on its output
        if ($bson instanceof Serializable) {
            return self::prepare($bson->bsonSerialize());
        }

        // Recurse on the PHP representation of Document and PackedArray types
        if ($bson instanceof Document || $bson instanceof PackedArray) {
            return self::prepare($bson->toPHP());
        }

        /* Serializable, Document, and PackedArray have already been handled.
         * Any remaining Type instances will not serialize as BSON arrays or
         * objects. */
        if ($bson instanceof Type) {
            return $bson;
        }

        if (is_array($bson) && self::isArrayEmptyOrIndexed($bson)) {
            $bson = new BSONArray($bson);
        }

        if (! $bson instanceof BSONArray && ! $bson instanceof BSONDocument) {
            /* If $bson is an object, any numeric keys may become inaccessible.
             * We can work around this by casting back to an array. */
            $bson = new BSONDocument((array) $bson);
        }

        foreach ($bson as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $bson[$key] = self::prepare($value);
            }
        }

        return $bson;
    }

    private static function isArrayEmptyOrIndexed(array $a): bool
    {
        if (empty($a)) {
            return true;
        }

        return array_keys($a) === range(0, count($a) - 1);
    }
}
