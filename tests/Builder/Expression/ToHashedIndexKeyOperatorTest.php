<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $toHashedIndexKey expression
 */
class ToHashedIndexKeyOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(val: 'string to hash'),
            ]),
            Stage::addFields(
                hashedVal: Expression::toHashedIndexKey(
                    Expression::stringFieldPath('val'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToHashedIndexKeyExample, $pipeline);
    }
}
