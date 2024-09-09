<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $searchMeta stage
 */
class SearchMetaStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(object(
                range: object(
                    path: 'year',
                    gte: 1998,
                    lt: 1999,
                ),
                count: object(type: 'total'),
            )),
        );

        $this->assertSamePipeline(Pipelines::SearchMetaExample, $pipeline);
    }
}
