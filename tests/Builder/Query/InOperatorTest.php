<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $in query
 */
class InOperatorTest extends PipelineTestCase
{
    public function testUseTheInOperatorToMatchValuesInAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                tags: Query::in(['home', 'school']),
            ),
        );

        $this->assertSamePipeline(Pipelines::InUseTheInOperatorToMatchValuesInAnArray, $pipeline);
    }

    public function testUseTheInOperatorWithARegularExpression(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                tags: Query::in([new Regex('^be'), new Regex('^st')]),
            ),
        );

        $this->assertSamePipeline(Pipelines::InUseTheInOperatorWithARegularExpression, $pipeline);
    }
}
