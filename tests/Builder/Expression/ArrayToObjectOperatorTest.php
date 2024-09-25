<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $arrayToObject expression
 */
class ArrayToObjectOperatorTest extends PipelineTestCase
{
    public function testArrayToObjectExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                dimensions: Expression::arrayToObject(
                    Expression::arrayFieldPath('dimensions'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ArrayToObjectArrayToObjectExample, $pipeline);
    }

    public function testObjectToArrayAndArrayToObjectExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                instock: Expression::objectToArray(
                    Expression::objectFieldPath('instock'),
                ),
            ),
            Stage::addFields(
                instock: Expression::concatArrays(
                    Expression::arrayFieldPath('instock'),
                    [
                        object(k: 'total', v: Expression::sum(
                            Expression::fieldPath('instock.v'),
                        )),
                    ],
                ),
            ),
            Stage::addFields(
                instock: Expression::arrayToObject(
                    Expression::arrayFieldPath('instock'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ArrayToObjectObjectToArrayAndArrayToObjectExample, $pipeline);
    }
}
