<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $pop update
 */
class PopOperatorTest extends UpdateTestCase
{
    public function testRemoveTheFirstItemOfAnArray(): void
    {
        $update = Update::pop(scores: -1);

        $this->assertSameUpdate(Pipelines::PopRemoveTheFirstItemOfAnArray, $update);
    }

    public function testRemoveTheLastItemOfAnArray(): void
    {
        $update = Update::pop(scores: 1);

        $this->assertSameUpdate(Pipelines::PopRemoveTheLastItemOfAnArray, $update);
    }
}
