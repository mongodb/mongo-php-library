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
 * New in MongoDB 4.4
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/
 * @internal
 */
final class MetaOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$meta';
    public const PROPERTIES = ['keyword' => 'keyword'];

    /** @var string $keyword */
    public readonly string $keyword;

    /**
     * @param string $keyword
     */
    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }
}
