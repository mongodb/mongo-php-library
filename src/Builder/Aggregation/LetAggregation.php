<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use stdClass;

/**
 * Defines variables for use within the scope of a subexpression and returns the result of the subexpression. Accepts named parameters.
 * Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/
 */
class LetAggregation implements ExpressionInterface
{
    public const NAME = '$let';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     */
    public Document|Serializable|stdClass|array $vars;

    /** @param ExpressionInterface|mixed $in The expression to evaluate. */
    public mixed $in;

    /**
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     * @param ExpressionInterface|mixed $in The expression to evaluate.
     */
    public function __construct(Document|Serializable|stdClass|array $vars, mixed $in)
    {
        $this->vars = $vars;
        $this->in = $in;
    }
}
