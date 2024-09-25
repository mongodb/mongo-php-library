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
 * Splits large change stream events that exceed 16 MB into smaller fragments returned in a change stream cursor.
 * You can only use $changeStreamSplitLargeEvent in a $changeStream pipeline and it must be the final stage in the pipeline.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStreamSplitLargeEvent/
 */
class ChangeStreamSplitLargeEventStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }

    public function getOperator(): string
    {
        return '$changeStreamSplitLargeEvent';
    }
}
