<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;

/**
 * Lists sampled queries for all collections or a specific collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/
 */
class ListSampledQueriesStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var Optional|non-empty-string $namespace */
    public readonly Optional|string $namespace;

    /**
     * @param Optional|non-empty-string $namespace
     */
    public function __construct(Optional|string $namespace = Optional::Undefined)
    {
        $this->namespace = $namespace;
    }

    public function getOperator(): string
    {
        return '$listSampledQueries';
    }
}
