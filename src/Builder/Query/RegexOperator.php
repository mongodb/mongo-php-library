<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Selects documents where values match a specified regular expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/
 * @internal
 */
final class RegexOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$regex';
    public const PROPERTIES = ['regex' => 'regex'];

    /** @var Regex $regex */
    public readonly Regex $regex;

    /**
     * @param Regex $regex
     */
    public function __construct(Regex $regex)
    {
        $this->regex = $regex;
    }
}
