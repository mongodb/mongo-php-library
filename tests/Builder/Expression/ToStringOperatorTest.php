<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $toString expression
 */
class ToStringOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedZipCode: Expression::toString(
                    Expression::stringFieldPath('zipcode'),
                ),
            ),
            Stage::sort(
                object(
                    convertedZipCode: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToStringExample, $pipeline);
    }
}
