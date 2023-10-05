<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToNull;
use MongoDB\Builder\Expression\ResolvesToString;

/**
 * Replaces all instances of a search string in an input string with a replacement string.
 * $replaceAll is both case-sensitive and diacritic-sensitive, and ignores any collation present on a collection.
 * New in version 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceAll/
 */
class ReplaceAllAggregation implements ResolvesToString
{
    public const NAME = '$replaceAll';
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
