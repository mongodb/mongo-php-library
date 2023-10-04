<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

class ProjectStage implements StageInterface
{
    public const NAME = '$project';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array<string, ExpressionInterface|Int64|bool|int|mixed> ...$specification */
    public array $specification;

    /**
     * @param ExpressionInterface|Int64|bool|int|mixed $specification
     */
    public function __construct(mixed ...$specification)
    {
        foreach($specification as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $specification arguments to be a map of ExpressionInterface|Int64|bool|int|mixed, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        if (\count($specification) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $specification, got %d.', 1, \count($specification)));
        }
        $this->specification = $specification;
    }
}
