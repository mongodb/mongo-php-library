<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $max update
 */
class MaxOperatorTest extends UpdateTestCase
{
    public function testUseMaxToCompareDates(): void
    {
        $update = Update::max(dateExpired: new UTCDateTime(1380499200000));

        $this->assertSameUpdate(Pipelines::MaxUseMaxToCompareDates, $update);
    }

    public function testUseMaxToCompareNumbers(): void
    {
        $update = Update::max(highScore: 950);

        $this->assertSameUpdate(Pipelines::MaxUseMaxToCompareNumbers, $update);
    }
}
