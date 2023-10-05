<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use stdClass;

class AddFieldsStage implements StageInterface
{
    public const NAME = '$addFields';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param stdClass<ExpressionInterface|mixed> ...$expression Specify the name of each field to add and set its value to an aggregation expression or an empty object. */
    public stdClass $expression;

    /**
     * @param ExpressionInterface|mixed ...$expression Specify the name of each field to add and set its value to an aggregation expression or an empty object.
     */
    public function __construct(mixed ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        foreach($expression as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $expression arguments to be a map of ExpressionInterface|mixed, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        $expression = (object) $expression;
        $this->expression = $expression;
    }
}
