<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Constraint\Constraint;
use ArrayObject;
use InvalidArgumentException;
use RuntimeException;
use stdClass;

/**
 * Constraint that checks if one document matches another.
 *
 * The expected value is passed in the constructor.
 */
class DocumentsMatchConstraint extends Constraint
{
    private $ignoreExtraKeysInRoot = false;
    private $ignoreExtraKeysInEmbedded = false;
    private $placeholders = [];
    /* TODO: This is not currently used, but was preserved from the design of
     * TestCase::assertMatchesDocument(), which would sort keys and then compare
     * documents as JSON strings. If the TODO item in matches() is implemented
     * to make document comparisons more efficient, we may consider supporting
     * this option. */
    private $sortKeys = false;
    private $value;

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
        parent::__construct();
        $this->value = $this->prepareBSON($value, true, $this->sortKeys);
        $this->ignoreExtraKeysInRoot = $ignoreExtraKeysInRoot;
        $this->ignoreExtraKeysInEmbedded = $ignoreExtraKeysInEmbedded;
        $this->placeholders = $placeholders;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'matches ' . json_encode($this->value);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other
     * @return boolean
     */
    protected function matches($other)
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

    /**
     * Compares two documents recursively.
     *
     * @param ArrayObject $expected
     * @param ArrayObject $actual
     * @param boolean     $ignoreExtraKeys
     * @throws RuntimeException if the documents do not match
     */
    private function assertEquals(ArrayObject $expected, ArrayObject $actual, $ignoreExtraKeys)
    {
        if (get_class($expected) !== get_class($actual)) {
            throw new RuntimeException(sprintf('$expected is %s but $actual is %s', get_class($expected), get_class($actual)));
        }

        foreach ($expected as $key => $expectedValue) {
            $actualHasKey = $actual->offsetExists($key);

            if (!$actualHasKey) {
                throw new RuntimeException('$actual is missing key: ' . $key);
            }

            if (in_array($expectedValue, $this->placeholders, true)) {
                continue;
            }

            $actualValue = $actual[$key];

            if (($expectedValue instanceof BSONArray && $actualValue instanceof BSONArray) ||
                ($expectedValue instanceof BSONDocument && $actualValue instanceof BSONDocument)) {
                $this->assertEquals($expectedValue, $actualValue, $this->ignoreExtraKeysInEmbedded);
                continue;
            }

            if (gettype($expectedValue) != gettype($actualValue) || $expectedValue != $actualValue) {
                throw new RuntimeException('$expectedValue != $actualValue for key: ' . $key);
            }
        }

        if ($ignoreExtraKeys) {
            return;
        }

        foreach ($actual as $key => $value) {
            if (!$expected->offsetExists($key)) {
                throw new RuntimeException('$actual has extra key: ' . $key);
            }
        }
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
        if ( ! is_array($bson) && ! is_object($bson)) {
            throw new InvalidArgumentException('$bson is not an array or object');
        }

        if ($isRoot && is_array($bson)) {
            $bson = (object) $bson;
        }

        if ($bson instanceof BSONArray || (is_array($bson) && $bson === array_values($bson))) {
            if ( ! $bson instanceof BSONArray) {
                $bson = new BSONArray($bson);
            }
        } else {
            if ( ! $bson instanceof BSONDocument) {
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
