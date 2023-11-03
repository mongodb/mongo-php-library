<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $regex query
 */
class RegexOperatorTest extends PipelineTestCase
{
    public function testPerformALIKEMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                sku: Query::regex('789$', ''),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexPerformALIKEMatch, $pipeline);
    }

    public function testPerformCaseInsensitiveRegularExpressionMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                sku: Query::regex('^ABC', 'i'),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexPerformCaseInsensitiveRegularExpressionMatch, $pipeline);
    }
}
