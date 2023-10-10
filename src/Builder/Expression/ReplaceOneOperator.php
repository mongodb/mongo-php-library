<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Encode;

/**
 * Replaces the first instance of a matched string in a given input.
 * New in version 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceOne/
 */
class ReplaceOneOperator implements ResolvesToString
{
    public const NAME = '$replaceOne';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ResolvesToNull|ResolvesToString|non-empty-string|null $input The string on which you wish to apply the find. Can be any valid expression that resolves to a string or a null. If input refers to a field that is missing, $replaceAll returns null. */
    public ResolvesToNull|ResolvesToString|null|string $input;

    /** @param ResolvesToNull|ResolvesToString|non-empty-string|null $find The string to search for within the given input. Can be any valid expression that resolves to a string or a null. If find refers to a field that is missing, $replaceAll returns null. */
    public ResolvesToNull|ResolvesToString|null|string $find;

    /** @param ResolvesToNull|ResolvesToString|non-empty-string|null $replacement The string to use to replace all matched instances of find in input. Can be any valid expression that resolves to a string or a null. */
    public ResolvesToNull|ResolvesToString|null|string $replacement;

    /**
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $input The string on which you wish to apply the find. Can be any valid expression that resolves to a string or a null. If input refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $find The string to search for within the given input. Can be any valid expression that resolves to a string or a null. If find refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $replacement The string to use to replace all matched instances of find in input. Can be any valid expression that resolves to a string or a null.
     */
    public function __construct(
        ResolvesToNull|ResolvesToString|null|string $input,
        ResolvesToNull|ResolvesToString|null|string $find,
        ResolvesToNull|ResolvesToString|null|string $replacement,
    ) {
        $this->input = $input;
        $this->find = $find;
        $this->replacement = $replacement;
    }
}
