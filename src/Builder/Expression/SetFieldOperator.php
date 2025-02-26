<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Adds, updates, or removes a specified field in a document. You can use $setField to add, update, or remove fields with names that contain periods (.) or start with dollar signs ($).
 * New in MongoDB 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/
 * @internal
 */
final class SetFieldOperator implements ResolvesToObject, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$setField';
    public const PROPERTIES = ['field' => 'field', 'input' => 'input', 'value' => 'value'];

    /** @var ResolvesToString|string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant. */
    public readonly ResolvesToString|string $field;

    /** @var Document|ResolvesToObject|Serializable|array|stdClass|string $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined. */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array|string $input;

    /**
     * @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $value The value that you want to assign to field. value can be any valid expression.
     * Set to $$REMOVE to remove field from the input document.
     */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param ResolvesToString|string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $value The value that you want to assign to field. value can be any valid expression.
     * Set to $$REMOVE to remove field from the input document.
     */
    public function __construct(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array|string $input,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $value,
    ) {
        $this->field = $field;
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->input = $input;
        $this->value = $value;
    }
}
