<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use InvalidArgumentException;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;
use stdClass;

use function MongoDB\object;
use function restore_error_handler;
use function set_error_handler;

use const E_USER_DEPRECATED;

/**
 * Test $out stage with deprecated arguments
 */
class OutStageLegacyTest extends PipelineTestCase
{
    public function testDeprecatedOutputToSameDatabaseWithObject(): void
    {
        $pipeline = $this->assertDeprecated(
            fn () => new Pipeline(
                Stage::group(
                    _id: Expression::stringFieldPath('author'),
                    books: Accumulator::push(Expression::stringFieldPath('title')),
                ),
                Stage::out(object(coll: 'authors')),
            ),
        );

        $this->assertSamePipeline(Pipelines::OutOutputToSameDatabase, $pipeline);
    }

    public function testDeprecatedOutputToADifferentDatabaseWithObject(): void
    {
        $pipeline = $this->assertDeprecated(
            fn () => new Pipeline(
                Stage::group(
                    _id: Expression::stringFieldPath('author'),
                    books: Accumulator::push(Expression::stringFieldPath('title')),
                ),
                Stage::out(object(db: 'reporting', coll: 'authors')),
            ),
        );

        $this->assertSamePipeline(Pipelines::OutOutputToADifferentDatabase, $pipeline);
    }

    public function testDeprecatedOutputToADifferentDatabaseWithSerializable(): void
    {
        $pipeline = $this->assertDeprecated(
            fn () => new Pipeline(
                Stage::group(
                    _id: Expression::stringFieldPath('author'),
                    books: Accumulator::push(Expression::stringFieldPath('title')),
                ),
                Stage::out(new class implements Serializable {
                    public function bsonSerialize(): stdClass
                    {
                        return (object) ['db' => 'reporting', 'coll' => 'authors'];
                    }
                }),
            ),
        );

        $this->assertSamePipeline(Pipelines::OutOutputToADifferentDatabase, $pipeline);
    }

    public function testDeprecatedOutputWithArray(): void
    {
        $pipeline = $this->assertDeprecated(
            fn () => new Pipeline(
                Stage::group(
                    _id: Expression::stringFieldPath('author'),
                    books: Accumulator::push(Expression::stringFieldPath('title')),
                ),
                Stage::out(['coll' => 'authors', 'db' => 'reporting']),
            ),
        );

        $this->assertSamePipeline(Pipelines::OutOutputToADifferentDatabase, $pipeline);
    }

    public function testDeprecatedOutputThrowsWithExtraArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$db and $timeseries arguments must not be passed');

        $this->assertDeprecated(
            fn () => Stage::out(object(coll: 'authors'), db: 'reporting'),
        );
    }

    public function testDeprecatedOutputThrowsWithMissingCollField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"coll" string field');

        $this->assertDeprecated(
            fn () => Stage::out(object(db: 'reporting')),
        );
    }

    private function assertDeprecated(callable $callable): mixed
    {
        $messages = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$messages): bool {
            $messages[] = $errstr;

            return true;
        }, E_USER_DEPRECATED);

        try {
            $result = $callable();
        } finally {
            restore_error_handler();
        }

        self::assertCount(1, $messages, 'Expected exactly one deprecation notice');

        return $result;
    }
}
