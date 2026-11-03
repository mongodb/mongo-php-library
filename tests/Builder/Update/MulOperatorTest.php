<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\BSON\Decimal128;
use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $mul update
 */
class MulOperatorTest extends UpdateTestCase
{
    public function testMultiplyTheValueOfAField(): void
    {
        $update = Update::mul(price: new Decimal128('1.25'), quantity: 2);

        $this->assertSameUpdate(Pipelines::MulMultiplyTheValueOfAField, $update);
    }
}
