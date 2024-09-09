<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sampleRate query
 */
class SampleRateOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::sampleRate(0.33),
            ),
            Stage::count('numMatches'),
        );

        $this->assertSamePipeline(Pipelines::SampleRateExample, $pipeline);
    }
}
