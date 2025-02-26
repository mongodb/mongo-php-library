<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Evaluates a series of case expressions. When it finds an expression which evaluates to true, $switch executes a specified expression and breaks out of the control flow.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
 * @internal
 */
final class SwitchOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$switch';
    public const PROPERTIES = ['branches' => 'branches', 'default' => 'default'];

    /**
     * @var BSONArray|PackedArray|array $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     */
    public readonly PackedArray|BSONArray|array $branches;

    /**
     * @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default;

    /**
     * @param BSONArray|PackedArray|array $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public function __construct(
        PackedArray|BSONArray|array $branches,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default = Optional::Undefined,
    ) {
        if (is_array($branches) && ! array_is_list($branches)) {
            throw new InvalidArgumentException('Expected $branches argument to be a list, got an associative array.');
        }

        $this->branches = $branches;
        $this->default = $default;
    }
}
