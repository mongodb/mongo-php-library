<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use stdClass;

/**
 * Reshapes each document in the stream, such as by adding new fields or removing existing fields. For each input document, outputs one document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/
 */
class ProjectStage implements StageInterface
{
    public const NAME = '$project';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param stdClass<ExpressionInterface|Int64|bool|int|mixed> ...$specification */
    public stdClass $specification;

    /**
     * @param ExpressionInterface|Int64|bool|int|mixed ...$specification
     */
    public function __construct(mixed ...$specification)
    {
        if (\count($specification) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $specification, got %d.', 1, \count($specification)));
        }
        foreach($specification as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $specification arguments to be a map of ExpressionInterface|Int64|bool|int|mixed, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        $specification = (object) $specification;
        $this->specification = $specification;
    }
}
