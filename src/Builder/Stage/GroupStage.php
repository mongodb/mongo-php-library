<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Groups input documents by a specified identifier expression and applies the accumulator expression(s), if specified, to each group. Consumes all input documents and outputs one document per each distinct group. The output documents only contain the identifier field and, if specified, accumulated fields.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/
 * @internal
 */
final class GroupStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$group';
    public const PROPERTIES = ['_id' => '_id', 'field' => null];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $_id The _id expression specifies the group key. If you specify an _id value of null, or any other constant value, the $group stage returns a single document that aggregates values across all of the input documents. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $_id;

    /** @var stdClass<AccumulatorInterface|Document|Serializable|array|stdClass> $field Computed using the accumulator operators. */
    public readonly stdClass $field;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $_id The _id expression specifies the group key. If you specify an _id value of null, or any other constant value, the $group stage returns a single document that aggregates values across all of the input documents.
     * @param AccumulatorInterface|Document|Serializable|array|stdClass ...$field Computed using the accumulator operators.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $_id,
        Document|Serializable|AccumulatorInterface|stdClass|array ...$field,
    ) {
        $this->_id = $_id;
        foreach($field as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $field arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $field = (object) $field;
        $this->field = $field;
    }
}
