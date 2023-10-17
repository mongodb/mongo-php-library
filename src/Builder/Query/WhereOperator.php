<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Matches documents that satisfy a JavaScript expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/where/
 */
class WhereOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var non-empty-string $function */
    public readonly string $function;

    /**
     * @param non-empty-string $function
     */
    public function __construct(string $function)
    {
        $this->function = $function;
    }

    public function getOperator(): string
    {
        return '$where';
    }
}
