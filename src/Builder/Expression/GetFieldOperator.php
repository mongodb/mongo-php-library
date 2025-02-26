<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use stdClass;

/**
 * Returns the value of a specified field from a document. You can use $getField to retrieve the value of fields with names that contain periods (.) or start with dollar signs ($).
 * New in MongoDB 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/
 * @internal
 */
final class GetFieldOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$getField';
    public const PROPERTIES = ['field' => 'field', 'input' => 'input'];

    /**
     * @var ResolvesToString|string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     */
    public readonly ResolvesToString|string $field;

    /**
     * @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /**
     * @param ResolvesToString|string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public function __construct(
        ResolvesToString|string $field,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input = Optional::Undefined,
    ) {
        $this->field = $field;
        $this->input = $input;
    }
}
