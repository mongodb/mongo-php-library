<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $function expression
 */
class FunctionOperatorTest extends PipelineTestCase
{
    public function testAlternativeToWhere(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::function(
                        body: <<<'JS'
                            function(name) {
                                return hex_md5(name) == "15b0a220baa16331e8d80e15367677ad";
                            }
                            JS,
                        args: [
                            Expression::stringFieldPath('name'),
                        ],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FunctionAlternativeToWhere, $pipeline);
    }

    public function testUsageExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                isFound: Expression::function(
                    body: <<<'JS'
                        function(name) {
                            return hex_md5(name) == "15b0a220baa16331e8d80e15367677ad"
                        }
                        JS,
                    args: [
                        Expression::stringFieldPath('name'),
                    ],
                ),
                message: Expression::function(
                    body: <<<'JS'
                        function(name, scores) {
                            let total = Array.sum(scores);
                            return `Hello ${name}. Your total score is ${total}.`
                        }
                        JS,
                    args: [
                        Expression::stringFieldPath('name'),
                        Expression::stringFieldPath('scores'),
                    ],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FunctionUsageExample, $pipeline);
    }
}
