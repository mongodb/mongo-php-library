<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Access available per-document metadata related to the aggregation operation.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/
 */
class MetaOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var string $keyword */
    public readonly string $keyword;

    /**
     * @param string $keyword
     */
    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }

    public function getOperator(): string
    {
        return '$meta';
    }
}
