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
 * Test $documentNumber accumulator
 */
class DocumentNumberAccumulatorTest extends PipelineTestCase
{
    public function testDocumentNumberForEachState(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(
                    quantity: Sort::Desc,
                ),
                output: object(
                    documentNumberForState: Accumulator::documentNumber(),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DocumentNumberDocumentNumberForEachState, $pipeline);
    }
}
