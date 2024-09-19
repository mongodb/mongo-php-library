<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $jsonSchema query
 */
class JsonSchemaOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::jsonSchema(object(
                    required: ['name', 'major', 'gpa', 'address'],
                    properties: object(
                        name: object(
                            bsonType: 'string',
                            description: 'must be a string and is required',
                        ),
                        address: object(
                            bsonType: 'object',
                            required: ['zipcode'],
                            properties: object(
                                zipcode: object(bsonType: 'string'),
                                street: object(bsonType: 'string'),
                            ),
                        ),
                    ),
                )),
            ),
        );

        $this->assertSamePipeline(Pipelines::JsonSchemaExample, $pipeline);
    }
}
