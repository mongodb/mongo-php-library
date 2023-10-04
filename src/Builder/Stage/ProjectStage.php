<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\ExpressionInterface;

class ProjectStage implements StageInterface
{
    public const NAME = '$project';
    public const ENCODE = 'single';

    /** @param list<ExpressionInterface|mixed> ...$specifications */
    public array $specifications;

    /**
     * @param ExpressionInterface|mixed $specifications
     */
    public function __construct(mixed ...$specifications)
    {
        if (\count($specifications) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($specifications)));
        }

        $this->specifications = $specifications;
    }
}
