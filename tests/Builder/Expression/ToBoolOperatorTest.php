<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $toBool expression
 */
class ToBoolOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedShippedFlag: Expression::switch(
                    branches: [
                        object(
                            case: Expression::eq(
                                Expression::fieldPath('shipped'),
                                'false',
                            ),
                            then: false,
                        ),
                        object(
                            case: Expression::eq(
                                Expression::fieldPath('shipped'),
                                '',
                            ),
                            then: false,
                        ),
                    ],
                    default: Expression::toBool(
                        Expression::fieldPath('shipped'),
                    ),
                ),
            ),
            Stage::match(
                convertedShippedFlag: false,
            ),
        );

        $this->assertSamePipeline(Pipelines::ToBoolExample, $pipeline);
    }
}
