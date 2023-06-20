<?php

namespace MongoDB\Tests\Comparator;

use MongoDB\BSON\Int64;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

use function is_int;
use function is_numeric;
use function is_string;
use function sprintf;

use const PHP_INT_SIZE;

class Int64Comparator extends Comparator
{
    public function accepts($expected, $actual)
    {
        // Only compare if either value is an Int64
        return ($expected instanceof Int64 && $this->isComparable($actual))
            || ($actual instanceof Int64 && $this->isComparable($expected));
    }

    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        if (PHP_INT_SIZE == 8) {
            // On 64-bit systems, compare integers directly
            $expectedValue = (int) $expected;
            $actualValue = (int) $actual;
        } else {
            // On 32-bit systems, compare integers as strings
            $expectedValue = (string) $expected;
            $actualValue = (string) $actual;
        }

        if ($expectedValue === $actualValue) {
            return;
        }

        throw new ComparisonFailure(
            $expected,
            $actual,
            '',
            '',
            false,
            sprintf(
                'Failed asserting that %s matches expected %s.',
                $this->exporter->export($actual),
                $this->exporter->export($expected)
            )
        );
    }

    private function isComparable($value): bool
    {
        return $value instanceof Int64 // Int64 instances
            || is_int($value) // Integer values
            || (is_string($value) && is_numeric($value)); // Numeric strings (is_numeric accepts floats)
    }
}
