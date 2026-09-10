<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $deserializeEJSON expression
 */
class DeserializeEJSONOperatorTest extends PipelineTestCase
{
    public function testDeserializeExtendedJSONDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                title: 'Inception',
            ),
            Stage::project(
                original: Expression::variable('ROOT'),
                serialized: Expression::serializeEJSON(
                    input: Expression::variable('ROOT'),
                ),
            ),
            Stage::project(
                title: Expression::fieldPath('original.title'),
                deserialized: Expression::deserializeEJSON(
                    input: Expression::fieldPath('serialized'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DeserializeEJSONDeserializeExtendedJSONDocument, $pipeline);
    }

    public function testDeserializeSpecificFields(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                title: 'Inception',
            ),
            Stage::project(
                title: 1,
                serializedMetadata: Expression::serializeEJSON(
                    input: object(
                        releaseDate: Expression::fieldPath('released'),
                        runtime: Expression::fieldPath('runtime'),
                        rating: Expression::fieldPath('imdb.rating'),
                    ),
                ),
            ),
            Stage::project(
                title: 1,
                metadata: Expression::deserializeEJSON(
                    input: Expression::fieldPath('serializedMetadata'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DeserializeEJSONDeserializeSpecificFields, $pipeline);
    }

    public function testParseJSONStringAndDeserialize(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(jsonData: '{"_id":{"$oid":"507f1f77bcf86cd799439011"},"title":"The Matrix","year":{"$numberInt":"1999"},"rating":{"$numberDouble":"8.7"}}'),
            ]),
            Stage::project(
                parsed: Expression::convert(
                    input: Expression::fieldPath('jsonData'),
                    to: 'object',
                ),
            ),
            Stage::project(
                movie: Expression::deserializeEJSON(
                    input: Expression::fieldPath('parsed'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DeserializeEJSONParseJSONStringAndDeserialize, $pipeline);
    }

    public function testUseOnErrorForErrorHandling(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::deserializeEJSON(
                    input: Expression::fieldPath('ejsonField'),
                    onError: object(error: 'Invalid EJSON format'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DeserializeEJSONUseOnErrorForErrorHandling, $pipeline);
    }
}
