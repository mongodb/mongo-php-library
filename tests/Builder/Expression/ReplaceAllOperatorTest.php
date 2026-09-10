<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $replaceAll expression
 */
class ReplaceAllOperatorTest extends PipelineTestCase
{
    public function testReplaceUsingAString(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: Expression::replaceAll(
                    input: Expression::stringFieldPath('item'),
                    find: 'blue paint',
                    replacement: 'red paint',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceAllReplaceUsingAString, $pipeline);
    }

    public function testReplaceUsingRegex(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: Expression::replaceAll(
                    input: Expression::stringFieldPath('item'),
                    find: new Regex('\bblue paint\b'),
                    replacement: 'red paint',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceAllReplaceUsingRegex, $pipeline);
    }
}
