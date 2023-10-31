<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Pipeline;
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
                // sku: \MongoDB\Builder\Query::regex('789$', ''),
                sku: new Regex('789$', ''),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexPerformALIKEMatch, $pipeline);
    }

    public function testPerformCaseInsensitiveRegularExpressionMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                // sku: \MongoDB\Builder\Query::regex('^ABC', 'i'),
                sku: new Regex('^ABC', 'i'),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexPerformCaseInsensitiveRegularExpressionMatch, $pipeline);
    }
}
