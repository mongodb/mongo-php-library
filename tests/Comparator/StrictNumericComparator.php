<?php

namespace MongoDB\Tests\Comparator;

use MongoDB\BSON\Int64;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\ScalarComparator;
use SebastianBergmann\Exporter\Exporter;

use function abs;
use function is_float;
use function is_infinite;
use function is_nan;
use function is_numeric;
use function is_string;
use function sprintf;

final class StrictNumericComparator extends ScalarComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return (is_numeric($expected) || $expected instanceof Int64)
            && (is_numeric($actual) || $actual instanceof Int64)
            && ! (is_string($expected) && is_string($actual));
    }

    /** @throws ComparisonFailure */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        if ($this->isInfinite($actual) && $this->isInfinite($expected)) {
            return;
        }

        if (is_string($expected) || is_string($actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                '',
                '',
                sprintf(
                    'Numeric value %s cannot be equal to a string value %s.',
                    is_string($expected) ? $actual : $expected,
                    is_string($actual) ? $actual : $expected,
                ),
            );
        }

        if (
            $expected instanceof Int64 || $actual instanceof Int64
                ? $expected != $actual
                : (
                    ($this->isInfinite($actual) xor $this->isInfinite($expected))
                    || ($this->isNan($actual) || $this->isNan($expected))
                    || abs($actual - $expected) > $delta
                )
        ) {
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
    }

    private function isInfinite(mixed $value): bool
    {
        return is_float($value) && is_infinite($value);
    }

    private function isNan(mixed $value): bool
    {
        return is_float($value) && is_nan($value);
    }
}
