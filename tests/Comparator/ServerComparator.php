<?php

namespace MongoDB\Tests\Comparator;

use MongoDB\Driver\Server;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

use function sprintf;

class ServerComparator extends Comparator
{
    public function accepts($expected, $actual): bool
    {
        return $expected instanceof Server && $actual instanceof Server;
    }

    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        if ($expected == $actual) {
            return;
        }

        throw new ComparisonFailure(
            $expected,
            $actual,
            '',
            '',
            sprintf(
                'Failed asserting that Server("%s:%d") matches expected Server("%s:%d").',
                $actual->getHost(),
                $actual->getPort(),
                $expected->getHost(),
                $expected->getPort(),
            ),
        );
    }
}
