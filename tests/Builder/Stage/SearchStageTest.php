<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $search stage
 */
class SearchStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::search(object(
                near: object(
                    path: 'released',
                    origin: new UTCDateTime(new DateTime('2011-09-01T00:00:00.000+00:00')),
                    pivot: 7776000000,
                ),
            )),
            Stage::project(_id: 0, title: 1, released: 1),
            Stage::limit(5),
            Stage::facet(
                docs: [],
                meta: new Pipeline(
                    Stage::replaceWith(Expression::variable('SEARCH_META')),
                    Stage::limit(1),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchExample, $pipeline);
    }
}
