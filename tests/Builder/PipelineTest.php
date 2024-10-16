<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function array_map;
use function iterator_to_array;
use function range;

class PipelineTest extends TestCase
{
    public function testEmptyPipeline(): void
    {
        $pipeline = new Pipeline([]);

        $this->assertSame([], iterator_to_array($pipeline));
    }

    public function testFromArray(): void
    {
        $pipeline = new Pipeline(
            ['$match' => ['tag' => 'foo']],
            [
                ['$sort' => ['_id' => 1]],
                ['$skip' => 10],
            ],
            ['$limit' => 5],
        );

        $expected = [
            ['$match' => ['tag' => 'foo']],
            ['$sort' => ['_id' => 1]],
            ['$skip' => 10],
            ['$limit' => 5],
        ];

        $this->assertSame($expected, iterator_to_array($pipeline));
    }

    public function testMergingPipeline(): void
    {
        $stages = array_map(
            fn (int $i) => $this->createMock(StageInterface::class),
            range(0, 7),
        );

        $pipeline = new Pipeline(
            $stages[0],
            $stages[1],
            new Pipeline($stages[2], $stages[3]),
            [$stages[4], $stages[5]],
            new Pipeline($stages[6]),
            [$stages[7]],
        );

        $this->assertSame($stages, iterator_to_array($pipeline));
    }

    public function testRejectNamedArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Named arguments are not supported for pipelines');

        new Pipeline(
            $this->createMock(StageInterface::class),
            foo: $this->createMock(StageInterface::class),
        );
    }
}
