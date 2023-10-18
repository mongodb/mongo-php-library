<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Javascript;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Defines a custom function.
 * New in MongoDB 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/
 */
class FunctionOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /**
     * @var Javascript|non-empty-string $body The function definition. You can specify the function definition as either BSON\JavaScript or string.
     * function(arg1, arg2, ...) { ... }
     */
    public readonly Javascript|string $body;

    /** @var BSONArray|PackedArray|array $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ]. */
    public readonly PackedArray|BSONArray|array $args;

    /** @var non-empty-string $lang */
    public readonly string $lang;

    /**
     * @param Javascript|non-empty-string $body The function definition. You can specify the function definition as either BSON\JavaScript or string.
     * function(arg1, arg2, ...) { ... }
     * @param BSONArray|PackedArray|array $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ].
     * @param non-empty-string $lang
     */
    public function __construct(Javascript|string $body, PackedArray|BSONArray|array $args, string $lang = 'js')
    {
        $this->body = $body;
        if (is_array($args) && ! array_is_list($args)) {
            throw new InvalidArgumentException('Expected $args argument to be a list, got an associative array.');
        }

        $this->args = $args;
        $this->lang = $lang;
    }

    public function getOperator(): string
    {
        return '$function';
    }
}
