<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $listSessions stage
 */
class ListSessionsStageTest extends PipelineTestCase
{
    public function testListAllSessions(): void
    {
        $pipeline = new Pipeline(
            Stage::listSessions(
                allUsers: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::ListSessionsListAllSessions, $pipeline);
    }

    public function testListAllSessionsForTheCurrentUser(): void
    {
        $pipeline = new Pipeline(
            Stage::listSessions(),
        );

        $this->assertSamePipeline(Pipelines::ListSessionsListAllSessionsForTheCurrentUser, $pipeline);
    }

    public function testListAllSessionsForTheSpecifiedUsers(): void
    {
        $pipeline = new Pipeline(
            Stage::listSessions(
                users: [
                    object(user: 'myAppReader', db: 'test'),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::ListSessionsListAllSessionsForTheSpecifiedUsers, $pipeline);
    }
}
