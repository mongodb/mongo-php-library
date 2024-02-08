<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $covariancePop accumulator
 */
class CovariancePopAccumulatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(
                    orderDate: Sort::Asc,
                ),
                output: object(
                    covariancePopForState: Accumulator::outputWindow(
                        Accumulator::covariancePop(
                            Expression::year(
                                Expression::dateFieldPath('orderDate'),
                            ),
                            Expression::intFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CovariancePopExample, $pipeline);
    }
}
