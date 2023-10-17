<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns information on active and/or dormant operations for the MongoDB deployment. To run, use the db.aggregate() method.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/
 */
class CurrentOpStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }

    public function getOperator(): string
    {
        return '$currentOp';
    }
}
