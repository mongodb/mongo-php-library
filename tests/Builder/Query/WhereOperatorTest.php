<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $where query
 */
class WhereOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::where(<<<'JS'
                    function() {
                        return hex_md5(this.name) == "9b53e667f30cd329dca1ec9e6a83e994"
                    }
                    JS),
            ),
            Stage::match(
                Query::expr(
                    Expression::function(
                        body: <<<'JS'
                            function(name) {
                                return hex_md5(name) == "9b53e667f30cd329dca1ec9e6a83e994";
                            }
                            JS,
                        args: [Expression::fieldPath('name')],
                        lang: 'js',
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::WhereExample, $pipeline);
    }
}
