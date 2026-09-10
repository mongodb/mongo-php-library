<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use DateTimeImmutable;
use DateTimeZone;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test in search
 */
class InOperatorTest extends PipelineTestCase
{
    public function testArrayValueFieldMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::in(
                    path: 'accounts',
                    value: [
                        371138,
                        371139,
                        371140,
                    ],
                ),
            ),
            Stage::project(
                _id: 0,
                name: 1,
                accounts: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::InArrayValueFieldMatch, $pipeline);
    }

    public function testCompoundQueryMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::in(
                            path: 'name',
                            value: [
                                'james sanchez',
                                'jennifer lawrence',
                            ],
                        ),
                    ],
                    should: [
                        Search::in(
                            path: '_id',
                            value: [
                                new ObjectId('5ca4bbcea2dd94ee58162a72'),
                                new ObjectId('5ca4bbcea2dd94ee58162a91'),
                            ],
                        ),
                    ],
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 1,
                name: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::InCompoundQueryMatch, $pipeline);
    }

    public function testSingleValueFieldMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::in(
                    path: 'birthdate',
                    value: [
                        new UTCDateTime(new DateTimeImmutable('1977-03-02T02:20:31', new DateTimeZone('UTC'))),
                        new UTCDateTime(new DateTimeImmutable('1977-03-01T00:00:00', new DateTimeZone('UTC'))),
                        new UTCDateTime(new DateTimeImmutable('1977-05-06T21:57:35', new DateTimeZone('UTC'))),
                    ],
                ),
            ),
            Stage::project(
                _id: 0,
                name: 1,
                birthdate: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::InSingleValueFieldMatch, $pipeline);
    }
}
