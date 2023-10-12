<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns information on active and/or dormant operations for the MongoDB deployment. To run, use the db.aggregate() method.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/
 */
readonly class CurrentOpStage implements StageInterface
{
    public const NAME = '$currentOp';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
