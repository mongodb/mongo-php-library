<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

use function MongoDB\object;

/**
 * Test $set update
 */
class SetOperatorTest extends UpdateTestCase
{
    public function testSetTopLevelFields(): void
    {
        $update = Update::set(
            quantity: 500,
            details: object(model: '2600', make: 'Fashionaires'),
            tags: ['coats', 'outerwear', 'clothing'],
        );

        $this->assertSameUpdate(Pipelines::SetSetTopLevelFields, $update);
    }

    public function testSetTopLevelFieldsInMultipleCalls(): void
    {
        $update = new Update(
            Update::set(quantity: 500),
            Update::set(
                details: object(model: '2600', make: 'Fashionaires'),
                tags: ['coats', 'outerwear', 'clothing'],
            ),
        );

        $this->assertSameUpdate(Pipelines::SetSetTopLevelFields, $update);
    }
}
