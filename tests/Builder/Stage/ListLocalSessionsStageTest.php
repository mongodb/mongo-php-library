<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $listLocalSessions stage
 */
class ListLocalSessionsStageTest extends PipelineTestCase
{
    public function testListAllLocalSessions(): void
    {
        $pipeline = new Pipeline(
            Stage::listLocalSessions(
                allUsers: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::ListLocalSessionsListAllLocalSessions, $pipeline);
    }

    public function testListAllLocalSessionsForTheCurrentUser(): void
    {
        $pipeline = new Pipeline(
            Stage::listLocalSessions(),
        );

        $this->assertSamePipeline(Pipelines::ListLocalSessionsListAllLocalSessionsForTheCurrentUser, $pipeline);
    }

    public function testListAllLocalSessionsForTheSpecifiedUsers(): void
    {
        $pipeline = new Pipeline(
            Stage::listLocalSessions(
                users: [
                    object(user: 'myAppReader', db: 'test'),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::ListLocalSessionsListAllLocalSessionsForTheSpecifiedUsers, $pipeline);
    }
}
