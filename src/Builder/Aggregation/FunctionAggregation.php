<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Model\BSONArray;

/**
 * Defines a custom function.
 * New in version 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/
 */
class FunctionAggregation implements ExpressionInterface
{
    public const NAME = '$function';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $body The function definition. You can specify the function definition as either BSON type Code or String. */
    public string $body;

    /** @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ]. */
    public PackedArray|BSONArray|array $args;

    /** @param non-empty-string $lang */
    public string $lang;

    /**
     * @param non-empty-string $body The function definition. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ].
     * @param non-empty-string $lang
     */
    public function __construct(string $body, PackedArray|BSONArray|array $args, string $lang)
    {
        $this->body = $body;
        if (\is_array($args) && ! \array_is_list($args)) {
            throw new \InvalidArgumentException('Expected $args argument to be a list, got an associative array.');
        }
        $this->args = $args;
        $this->lang = $lang;
    }
}
