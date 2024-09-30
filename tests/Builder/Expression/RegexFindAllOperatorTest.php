<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $regexFindAll expression
 */
class RegexFindAllOperatorTest extends PipelineTestCase
{
    public function testIOption(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                returnObject: Expression::regexFindAll(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line', 'i'),
                ),
            ),
            Stage::addFields(
                returnObject: Expression::regexFindAll(
                    input: Expression::stringFieldPath('description'),
                    regex: 'line',
                    options: 'i',
                ),
            ),
            Stage::addFields(
                returnObject: Expression::regexFindAll(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line'),
                    options: 'i',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexFindAllIOption, $pipeline);
    }

    public function testRegexFindAllAndItsOptions(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                returnObject: Expression::regexFindAll(
                    input: Expression::stringFieldPath('description'),
                    regex: new Regex('line'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexFindAllRegexFindAllAndItsOptions, $pipeline);
    }

    public function testUseCapturedGroupingsToParseUserName(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                names: Expression::regexFindAll(
                    input: Expression::stringFieldPath('comment'),
                    regex: new Regex('([a-z0-9_.+-]+)@[a-z0-9_.+-]+\\.[a-z0-9_.+-]+', 'i'),
                ),
            ),
            Stage::set(
                names: Expression::reduce(
                    input: Expression::arrayFieldPath('names.captures'),
                    initialValue: [],
                    in: Expression::concatArrays(
                        Expression::variable('value'),
                        Expression::variable('this'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexFindAllUseCapturedGroupingsToParseUserName, $pipeline);
    }

    public function testUseRegexFindAllToParseEmailFromString(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                email: Expression::regexFindAll(
                    input: Expression::stringFieldPath('comment'),
                    regex: new Regex('[a-z0-9_.+-]+@[a-z0-9_.+-]+\\.[a-z0-9_.+-]+', 'i'),
                ),
            ),
            Stage::set(
                email: Expression::stringFieldPath('email.match'),
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexFindAllUseRegexFindAllToParseEmailFromString, $pipeline);
    }
}
