<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $setOnInsert update
 */
class SetOnInsertOperatorTest extends UpdateTestCase
{
    public function testUpsertWithSetOnInsert(): void
    {
        $update = new Update(
            Update::set(item: 'apple'),
            Update::setOnInsert(defaultQty: 100),
        );

        $this->assertSameUpdate(Pipelines::SetOnInsertUpsertWithSetOnInsert, $update);
    }
}
