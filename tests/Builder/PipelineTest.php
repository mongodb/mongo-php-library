<?php

namespace MongoDB\Tests\Builder;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;

use function array_map;
use function iterator_to_array;
use function range;

class PipelineTest extends TestCase
{
    public function testMergingPipeline(): void
    {
        $stages = array_map(
            fn (int $i) => $this->createMock(StageInterface::class),
            range(0, 5),
        );

        $pipeline = new Pipeline(
            $stages[0],
            $stages[1],
            new Pipeline($stages[2], $stages[3]),
            $stages[4],
            new Pipeline($stages[5]),
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

    public function testRejectEmptyPipeline(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one stage must be provided');

        new Pipeline();
    }
}
