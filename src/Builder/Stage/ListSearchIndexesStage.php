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
 * Returns information about existing Atlas Search indexes on a specified collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/
 */
class ListSearchIndexesStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var Optional|string $id The id of the index to return information about. */
    public readonly Optional|string $id;

    /** @var Optional|string $name The name of the index to return information about. */
    public readonly Optional|string $name;

    /**
     * @param Optional|string $id The id of the index to return information about.
     * @param Optional|string $name The name of the index to return information about.
     */
    public function __construct(
        Optional|string $id = Optional::Undefined,
        Optional|string $name = Optional::Undefined,
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getOperator(): string
    {
        return '$listSearchIndexes';
    }
}
