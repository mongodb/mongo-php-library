<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setIntersection expression
 */
class SetIntersectionOperatorTest extends PipelineTestCase
{
    public function testElementsArrayExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                flowerFieldA: 1,
                flowerFieldB: 1,
                commonToBoth: Expression::setIntersection(
                    Expression::arrayFieldPath('flowerFieldA'),
                    Expression::arrayFieldPath('flowerFieldB'),
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::SetIntersectionElementsArrayExample, $pipeline);
    }

    public function testRetrieveDocumentsForRolesGrantedToTheCurrentUser(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::not(
                        Expression::eq(
                            Expression::setIntersection(
                                Expression::arrayFieldPath('allowedRoles'),
                                Expression::variable('USER_ROLES.role'),
                            ),
                            [],
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetIntersectionRetrieveDocumentsForRolesGrantedToTheCurrentUser, $pipeline);
    }
}
