<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $serializeEJSON expression
 */
class SerializeEJSONOperatorTest extends PipelineTestCase
{
    public function testCanonicalExtendedJSONExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                title: 'Inception',
            ),
            Stage::project(
                ejson: Expression::serializeEJSON(
                    input: Expression::variable('ROOT'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SerializeEJSONCanonicalExtendedJSONExample, $pipeline);
    }

    public function testConvertToJSONString(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                title: 'The Godfather',
            ),
            Stage::project(
                title: 1,
                jsonString: Expression::toString(
                    Expression::serializeEJSON(
                        input: Expression::variable('ROOT'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SerializeEJSONConvertToJSONString, $pipeline);
    }

    public function testRelaxedExtendedJSONExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                title: 'Inception',
            ),
            Stage::project(
                ejson: Expression::serializeEJSON(
                    input: Expression::variable('ROOT'),
                    relaxed: true,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SerializeEJSONRelaxedExtendedJSONExample, $pipeline);
    }

    public function testSerializeSpecificFields(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                year: Query::gte(2010),
            ),
            Stage::project(
                title: 1,
                metadataEJSON: Expression::serializeEJSON(
                    input: object(
                        releaseDate: Expression::dateFieldPath('released'),
                        runtime: Expression::intFieldPath('runtime'),
                        imdbRating: Expression::doubleFieldPath('imdb.rating'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SerializeEJSONSerializeSpecificFields, $pipeline);
    }

    public function testUseOnErrorForErrorHandling(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                title: 1,
                ejson: Expression::serializeEJSON(
                    input: Expression::stringFieldPath('customField'),
                    onError: object(error: 'Serialization failed'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SerializeEJSONUseOnErrorForErrorHandling, $pipeline);
    }
}
