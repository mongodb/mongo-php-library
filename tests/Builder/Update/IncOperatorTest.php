<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $inc update
 */
class IncOperatorTest extends UpdateTestCase
{
    public function testIncrementFields(): void
    {
        $update = Update::inc(...['quantity' => -2, 'metrics.orders' => 1]);

        $this->assertSameUpdate(Pipelines::IncIncrementFields, $update);
    }
}
