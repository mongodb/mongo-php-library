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
 * Test $locf accumulator
 */
class LocfAccumulatorTest extends PipelineTestCase
{
    public function testFillMissingValuesWithTheLastObservedValue(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                sortBy: object(
                    time: Sort::Asc,
                ),
                output: object(
                    price: Accumulator::locf(
                        Expression::numberFieldPath('price'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LocfFillMissingValuesWithTheLastObservedValue, $pipeline);
    }
}
