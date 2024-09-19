<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $not query
 */
class NotOperatorTest extends PipelineTestCase
{
    public function testRegularExpressions(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                price: Query::not(
                    new Regex('^p.*'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NotRegularExpressions, $pipeline);
    }

    public function testSyntax(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                price: Query::not(
                    Query::gt(1.99),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NotSyntax, $pipeline);
    }
}
