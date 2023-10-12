<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;

/**
 * Applies a regular expression (regex) to a string and returns a boolean that indicates if a match is found or not.
 * New in version 4.2.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/
 */
readonly class RegexMatchOperator implements ResolvesToBool
{
    public const NAME = '$regexMatch';
    public const ENCODE = Encode::Object;

    /** @param ResolvesToString|non-empty-string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string. */
    public ResolvesToString|string $input;

    /** @param Regex|ResolvesToString|non-empty-string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options) */
    public Regex|ResolvesToString|string $regex;

    /** @param Optional|non-empty-string $options */
    public Optional|string $options;

    /**
     * @param ResolvesToString|non-empty-string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|non-empty-string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|non-empty-string $options
     */
    public function __construct(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->regex = $regex;
        $this->options = $options;
    }
}
