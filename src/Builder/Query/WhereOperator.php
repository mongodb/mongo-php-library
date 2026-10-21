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
 * @internal
 */
final class WhereOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$where';
    public const PROPERTIES = ['function' => 'function'];

    /** @var Javascript|string $function */
    public readonly Javascript|string $function;

    /** @param Javascript|string $function */
    public function __construct(Javascript|string $function)
    {
        if (is_string($function)) {
            $function = new Javascript($function);
        }

        $this->function = $function;
    }
}
