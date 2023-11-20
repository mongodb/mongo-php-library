<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $all query
 */
class AllOperatorTest extends PipelineTestCase
{
    public function testUseAllToMatchValues(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                tags: Query::all('appliance', 'school', 'book'),
            ),
        );

        $this->assertSamePipeline(Pipelines::AllUseAllToMatchValues, $pipeline);
    }

    public function testUseAllWithElemMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: Query::all(
                    Query::elemMatch(
                        Query::query(
                            size: 'M',
                            num: Query::gt(50),
                        ),
                    ),
                    Query::elemMatch(
                        Query::query(
                            num: 100,
                            color: 'green',
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AllUseAllWithElemMatch, $pipeline);
    }
}
