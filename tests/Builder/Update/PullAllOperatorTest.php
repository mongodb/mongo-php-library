<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $pullAll update
 */
class PullAllOperatorTest extends UpdateTestCase
{
    public function testRemoveMultipleValuesFromAnArray(): void
    {
        $update = Update::pullAll(scores: [0, 5]);

        $this->assertSameUpdate(Pipelines::PullAllRemoveMultipleValuesFromAnArray, $update);
    }
}
