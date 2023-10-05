<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;

/**
 * Returns information about existing Atlas Search indexes on a specified collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/
 */
class ListSearchIndexesStage implements StageInterface
{
    public const NAME = '$listSearchIndexes';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Optional|non-empty-string $id The id of the index to return information about. */
    public Optional|string $id;

    /** @param Optional|non-empty-string $name The name of the index to return information about. */
    public Optional|string $name;

    /**
     * @param Optional|non-empty-string $id The id of the index to return information about.
     * @param Optional|non-empty-string $name The name of the index to return information about.
     */
    public function __construct(
        Optional|string $id = Optional::Undefined,
        Optional|string $name = Optional::Undefined,
    ) {
        $this->id = $id;
        $this->name = $name;
    }
}
