<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

class AddFieldsStage implements StageInterface
{
    public const NAME = '$addFields';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array<string, ExpressionInterface|mixed> ...$expression Specify the name of each field to add and set its value to an aggregation expression or an empty object. */
    public array $expression;

    /**
     * @param ExpressionInterface|mixed $expression Specify the name of each field to add and set its value to an aggregation expression or an empty object.
     */
    public function __construct(mixed ...$expression)
    {
        foreach($expression as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $expression arguments to be a map of ExpressionInterface|mixed, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        $this->expression = $expression;
    }
}
