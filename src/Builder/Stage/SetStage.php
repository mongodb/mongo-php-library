<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use stdClass;

/**
 * Adds new fields to documents. Outputs documents that contain all existing fields from the input documents and newly added fields.
 * Alias for $addFields.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/
 */
class SetStage implements StageInterface
{
    public const NAME = '$set';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param stdClass<ExpressionInterface|mixed> ...$field */
    public stdClass $field;

    /**
     * @param ExpressionInterface|mixed ...$field
     */
    public function __construct(mixed ...$field)
    {
        if (\count($field) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }
        foreach($field as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $field arguments to be a map of ExpressionInterface|mixed, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        $field = (object) $field;
        $this->field = $field;
    }
}
