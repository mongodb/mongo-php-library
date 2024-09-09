<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $unsetField expression
 */
class UnsetFieldOperatorTest extends PipelineTestCase
{
    public function testRemoveASubfield(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::setField(
                    field: 'price',
                    input: Expression::variable('ROOT'),
                    value: Expression::unsetField(
                        field: 'euro',
                        input: Expression::getField('price'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::UnsetFieldRemoveASubfield, $pipeline);
    }

    public function testRemoveFieldsThatContainPeriods(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::unsetField(
                    field: 'price.usd',
                    input: Expression::variable('ROOT'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::UnsetFieldRemoveFieldsThatContainPeriods, $pipeline);
    }

    public function testRemoveFieldsThatStartWithADollarSign(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::unsetField(
                    field: Expression::literal('$price'),
                    input: Expression::variable('ROOT'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::UnsetFieldRemoveFieldsThatStartWithADollarSign, $pipeline);
    }
}
