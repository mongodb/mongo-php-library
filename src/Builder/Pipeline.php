<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use ArrayIterator;
use IteratorAggregate;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use Traversable;

use function array_is_list;
use function array_merge;

/**
 * An aggregation pipeline consists of one or more stages that process documents.
 *
 * @see https://www.mongodb.com/docs/manual/core/aggregation-pipeline/
 *
 * @psalm-immutable
 * @implements IteratorAggregate<StageInterface>
 */
class Pipeline implements IteratorAggregate
{
    /** @var StageInterface[] */
    private readonly array $stages;

    /** @no-named-arguments */
    public function __construct(StageInterface|Pipeline ...$stagesOrPipelines)
    {
        if (! array_is_list($stagesOrPipelines)) {
            throw new InvalidArgumentException('Named arguments are not supported for pipelines');
        }

        $stages = [];

        foreach ($stagesOrPipelines as $stageOrPipeline) {
            if ($stageOrPipeline instanceof Pipeline) {
                $stages = array_merge($stages, $stageOrPipeline->stages);
            } else {
                $stages[] = $stageOrPipeline;
            }
        }

        $this->stages = $stages;
    }

    /** @return Traversable<StageInterface> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->stages);
    }
}
