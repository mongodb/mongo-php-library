<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test hasAncestor search
 */
class HasAncestorOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::hasAncestor(
                    ancestorPath: 'funding_rounds',
                    operator: Search::equals(
                        path: 'funding_rounds.funded_year',
                        value: 2005,
                    ),
                ),
                returnScope: object(path: 'funding_rounds.investments'),
                returnStoredSource: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::HasAncestorExample, $pipeline);
    }
}
