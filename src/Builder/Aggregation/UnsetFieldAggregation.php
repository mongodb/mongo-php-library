<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Expression\ResolvesToString;

class UnsetFieldAggregation implements ResolvesToObject
{
    public const NAME = '$unsetField';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ResolvesToString|non-empty-string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant. */
    public ResolvesToString|string $field;

    /** @param Document|ResolvesToObject|Serializable|array|object $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined. */
    public array|object $input;

    /**
     * @param ResolvesToString|non-empty-string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|object $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     */
    public function __construct(ResolvesToString|string $field, array|object $input)
    {
        $this->field = $field;
        $this->input = $input;
    }
}
