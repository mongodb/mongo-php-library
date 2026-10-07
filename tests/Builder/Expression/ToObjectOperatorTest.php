<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toObject expression
 */
class ToObjectOperatorTest extends PipelineTestCase
{
    public function testConvertStringToObject(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                parsedConfig: Expression::toObject(
                    Expression::fieldPath('config'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToObjectConvertStringToObject, $pipeline);
    }
}
