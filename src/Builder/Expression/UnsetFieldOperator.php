<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * You can use $unsetField to remove fields with names that contain periods (.) or that start with dollar signs ($).
 * $unsetField is an alias for $setField using $$REMOVE to remove fields.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/
 * @internal
 */
final class UnsetFieldOperator implements ResolvesToObject, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$unsetField';
    public const PROPERTIES = ['field' => 'field', 'input' => 'input'];

    /** @var ResolvesToString|string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant. */
    public readonly ResolvesToString|string $field;

    /** @var Document|ResolvesToObject|Serializable|array|stdClass|string $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined. */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array|string $input;

    /**
     * @param ResolvesToString|string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     */
    public function __construct(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array|string $input,
    ) {
        $this->field = $field;
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->input = $input;
    }
}
