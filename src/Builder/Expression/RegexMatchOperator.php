<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;

/**
 * Applies a regular expression (regex) to a string and returns a boolean that indicates if a match is found or not.
 * New in MongoDB 4.2.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/
 */
class RegexMatchOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var ResolvesToString|string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string. */
    public readonly ResolvesToString|string $input;

    /** @var Regex|ResolvesToString|string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options) */
    public readonly Regex|ResolvesToString|string $regex;

    /** @var Optional|string $options */
    public readonly Optional|string $options;

    /**
     * @param ResolvesToString|string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|string $options
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

    public function getOperator(): string
    {
        return '$regexMatch';
    }
}
