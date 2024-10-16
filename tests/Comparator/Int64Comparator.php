<?php

namespace MongoDB\Tests\Comparator;

use MongoDB\BSON\Int64;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;

use function is_numeric;
use function sprintf;

class Int64Comparator extends Comparator
{
    public function accepts($expected, $actual): bool
    {
        // Only compare if either value is an Int64 and the other value is numeric
        return ($expected instanceof Int64 && $this->isComparable($actual))
            || ($actual instanceof Int64 && $this->isComparable($expected));
    }

    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        if ($expected == $actual) {
            return;
        }

        $exporter = new Exporter();

        throw new ComparisonFailure(
            $expected,
            $actual,
            '',
            '',
            sprintf(
                'Failed asserting that %s matches expected %s.',
                $exporter->export($actual),
                $exporter->export($expected),
            ),
        );
    }

    private function isComparable($value): bool
    {
        return $value instanceof Int64 || is_numeric($value);
    }
}
