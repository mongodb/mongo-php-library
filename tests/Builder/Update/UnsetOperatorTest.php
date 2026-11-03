<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $unset update
 */
class UnsetOperatorTest extends UpdateTestCase
{
    public function testRemoveFields(): void
    {
        $update = Update::unset(
            'quantity',
            Expression::stringFieldPath('instock'),
        );

        $this->assertSameUpdate(Pipelines::UnsetRemoveFields, $update);
    }
}
