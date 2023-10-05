<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Aggregation\AccumulatorInterface;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use stdClass;

class GroupStage implements StageInterface
{
    public const NAME = '$group';
    public const ENCODE = \MongoDB\Builder\Encode::Group;

    /** @param ExpressionInterface|mixed $_id The _id expression specifies the group key. If you specify an _id value of null, or any other constant value, the $group stage returns a single document that aggregates values across all of the input documents. */
    public mixed $_id;

    /** @param stdClass<AccumulatorInterface> ...$field Computed using the accumulator operators. */
    public stdClass $field;

    /**
     * @param ExpressionInterface|mixed $_id The _id expression specifies the group key. If you specify an _id value of null, or any other constant value, the $group stage returns a single document that aggregates values across all of the input documents.
     * @param AccumulatorInterface ...$field Computed using the accumulator operators.
     */
    public function __construct(mixed $_id, AccumulatorInterface ...$field)
    {
        $this->_id = $_id;
        if (\count($field) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }
        foreach($field as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $field arguments to be a map of AccumulatorInterface, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        $field = (object) $field;
        $this->field = $field;
    }
}
