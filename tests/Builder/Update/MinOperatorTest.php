<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $min update
 */
class MinOperatorTest extends UpdateTestCase
{
    public function testUseMinToCompareDates(): void
    {
        $update = Update::min(dateEntered: new UTCDateTime(1380067200000));

        $this->assertSameUpdate(Pipelines::MinUseMinToCompareDates, $update);
    }

    public function testUseMinToCompareNumbers(): void
    {
        $update = Update::min(lowScore: 150);

        $this->assertSameUpdate(Pipelines::MinUseMinToCompareNumbers, $update);
    }
}
