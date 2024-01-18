<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Javascript;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;

use function is_string;

/**
 * Matches documents that satisfy a JavaScript expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/where/
 */
class WhereOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Javascript|non-empty-string $function */
    public readonly Javascript|string $function;

    /**
     * @param Javascript|non-empty-string $function
     */
    public function __construct(Javascript|string $function)
    {
        if (is_string($function)) {
            $function = new Javascript($function);
        }

        $this->function = $function;
    }

    public function getOperator(): string
    {
        return '$where';
    }
}
