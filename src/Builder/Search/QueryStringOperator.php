<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\SearchOperatorInterface;

/**
 * New in MongoDB 5.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/queryString/
 * @internal
 */
final class QueryStringOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'queryString';
    public const PROPERTIES = ['defaultPath' => 'defaultPath', 'query' => 'query'];

    /** @var array|string $defaultPath */
    public readonly array|string $defaultPath;

    /** @var string $query */
    public readonly string $query;

    /**
     * @param array|string $defaultPath
     * @param string $query
     */
    public function __construct(array|string $defaultPath, string $query)
    {
        $this->defaultPath = $defaultPath;
        $this->query = $query;
    }
}
