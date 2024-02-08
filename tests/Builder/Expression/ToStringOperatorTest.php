<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

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
                convertedZipCode: Sort::Asc,
            ),
        );

        $this->assertSamePipeline(Pipelines::ToStringExample, $pipeline);
    }
}
