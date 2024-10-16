<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setField expression
 */
class SetFieldOperatorTest extends PipelineTestCase
{
    public function testAddFieldsThatContainPeriods(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::setField(
                    field: 'price.usd',
                    input: Expression::variable('ROOT'),
                    value: Expression::fieldPath('price'),
                ),
            ),
            Stage::unset('price'),
        );

        $this->assertSamePipeline(Pipelines::SetFieldAddFieldsThatContainPeriods, $pipeline);
    }

    public function testAddFieldsThatStartWithADollarSign(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::setField(
                    field: Expression::literal('$price'),
                    input: Expression::variable('ROOT'),
                    value: Expression::fieldPath('price'),
                ),
            ),
            Stage::unset('price'),
        );

        $this->assertSamePipeline(Pipelines::SetFieldAddFieldsThatStartWithADollarSign, $pipeline);
    }

    public function testRemoveFieldsThatContainPeriods(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::setField(
                    field: 'price.usd',
                    input: Expression::variable('ROOT'),
                    value: Expression::variable('REMOVE'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetFieldRemoveFieldsThatContainPeriods, $pipeline);
    }

    public function testRemoveFieldsThatStartWithADollarSign(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::setField(
                    field: Expression::literal('$price'),
                    input: Expression::variable('ROOT'),
                    value: Expression::variable('REMOVE'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetFieldRemoveFieldsThatStartWithADollarSign, $pipeline);
    }

    public function testUpdateFieldsThatContainPeriods(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                _id: 1,
            ),
            Stage::replaceWith(
                Expression::setField(
                    field: 'price.usd',
                    input: Expression::variable('ROOT'),
                    value: 49.99,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetFieldUpdateFieldsThatContainPeriods, $pipeline);
    }

    public function testUpdateFieldsThatStartWithADollarSign(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                _id: 1,
            ),
            Stage::replaceWith(
                Expression::setField(
                    field: Expression::literal('$price'),
                    input: Expression::variable('ROOT'),
                    value: 49.99,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetFieldUpdateFieldsThatStartWithADollarSign, $pipeline);
    }
}
