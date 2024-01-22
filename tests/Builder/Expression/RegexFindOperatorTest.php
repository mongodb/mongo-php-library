<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $regexFind expression
 */
class RegexFindOperatorTest extends PipelineTestCase
{
    public function testIOption(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                returnObject: Expression::regexFind(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line', 'i'),
                ),
            ),
            Stage::addFields(
                returnObject: Expression::regexFind(
                    input: Expression::stringFieldPath('description'),
                    regex: 'line',
                    options: 'i',
                ),
            ),
            Stage::addFields(
                returnObject: Expression::regexFind(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line'),
                    options: 'i',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexFindIOption, $pipeline);
    }

    public function testRegexFindAndItsOptions(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                returnObject: Expression::regexFind(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexFindRegexFindAndItsOptions, $pipeline);
    }
}
