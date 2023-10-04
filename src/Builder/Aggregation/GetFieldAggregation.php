<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;

class GetFieldAggregation implements ExpressionInterface
{
    public const NAME = '$getField';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     */
    public string $field;

    /**
     * @param ExpressionInterface|Optional|mixed $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public mixed $input;

    /**
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     * @param ExpressionInterface|Optional|mixed $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public function __construct(string $field, mixed $input = Optional::Undefined)
    {
        $this->field = $field;
        $this->input = $input;
    }
}
