<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $regexMatch expression
 */
class RegexMatchOperatorTest extends PipelineTestCase
{
    public function testIOption(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                result: Expression::regexMatch(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line', 'i'),
                ),
            ),
            Stage::addFields(
                result: Expression::regexMatch(
                    input: Expression::stringFieldPath('description'),
                    regex: 'line',
                    options: 'i',
                ),
            ),
            Stage::addFields(
                result: Expression::regexMatch(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line'),
                    options: 'i',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexMatchIOption, $pipeline);
    }

    public function testRegexMatchAndItsOptions(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                result: Expression::regexMatch(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line', ''),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexMatchRegexMatchAndItsOptions, $pipeline);
    }

    public function testUseRegexMatchToCheckEmailAddress(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                category: Expression::cond(
                    if: Expression::regexMatch(
                        input: Expression::stringFieldPath('comment'),
                        regex: new Regex('[a-z0-9_.+-]+@mongodb.com', 'i'),
                    ),
                    then: 'Employee',
                    else: 'External',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexMatchUseRegexMatchToCheckEmailAddress, $pipeline);
    }
}
