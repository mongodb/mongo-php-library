<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $redact stage
 */
class RedactStageTest extends PipelineTestCase
{
    public function testEvaluateAccessAtEveryDocumentLevel(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                year: 2014,
            ),
            Stage::redact(
                Expression::cond(
                    if: Expression::gt(
                        Expression::size(
                            Expression::setIntersection(
                                Expression::arrayFieldPath('tags'),
                                ['STLW', 'G'],
                            ),
                        ),
                        0,
                    ),
                    then: Expression::variable('DESCEND'),
                    else: Expression::variable('PRUNE'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RedactEvaluateAccessAtEveryDocumentLevel, $pipeline);
    }

    public function testExcludeAllFieldsAtAGivenLevel(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                status: 'A',
            ),
            Stage::redact(
                Expression::cond(
                    if: Expression::eq(
                        Expression::intFieldPath('level'),
                        5,
                    ),
                    then: Expression::variable('PRUNE'),
                    else: Expression::variable('DESCEND'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RedactExcludeAllFieldsAtAGivenLevel, $pipeline);
    }
}
