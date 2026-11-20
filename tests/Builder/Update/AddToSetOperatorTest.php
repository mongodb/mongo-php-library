<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $addToSet update
 */
class AddToSetOperatorTest extends UpdateTestCase
{
    public function testAddToArray(): void
    {
        $update = Update::addToSet(tags: 'accessories');

        $this->assertSameUpdate(Pipelines::AddToSetAddToArray, $update);
    }

    public function testUseEachModifier(): void
    {
        // @todo Use builder for $each
        $update = Update::addToSet(tags: ['$each' => ['camera', 'electronics', 'accessories']]);

        $this->assertSameUpdate(Pipelines::AddToSetUseEachModifier, $update);
    }

    public function testValueAlreadyExists(): void
    {
        $update = Update::addToSet(tags: 'camera');

        $this->assertSameUpdate(Pipelines::AddToSetValueAlreadyExists, $update);
    }
}
