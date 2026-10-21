<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use DateTimeImmutable;
use DateTimeZone;
use MongoDB\BSON\Binary;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function hex2bin;

/**
 * Test equals search
 */
class EqualsOperatorTest extends PipelineTestCase
{
    public function testBoolean(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'verified_user',
                    value: true,
                ),
            ),
            Stage::project(
                name: 1,
                _id: 0,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsBoolean, $pipeline);
    }

    public function testDate(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'account_created',
                    value: new UTCDateTime(new DateTimeImmutable('2022-05-04T05:01:08', new DateTimeZone('UTC'))),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsDate, $pipeline);
    }

    public function testNull(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'job_title',
                    value: null,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsNull, $pipeline);
    }

    public function testNumber(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'employee_number',
                    value: 259,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsNumber, $pipeline);
    }

    public function testObjectId(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'teammates',
                    value: new ObjectId('5a9427648b0beebeb69589a1'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsObjectId, $pipeline);
    }

    public function testString(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'name',
                    value: 'jim hall',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsString, $pipeline);
    }

    public function testUUID(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::equals(
                    path: 'uuid',
                    value: new Binary(hex2bin('fac32260b5114c698485a2be5b7dda9e'), Binary::TYPE_UUID),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqualsUUID, $pipeline);
    }
}
