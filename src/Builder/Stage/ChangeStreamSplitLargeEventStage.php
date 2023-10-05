<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;

/**
 * Splits large change stream events that exceed 16 MB into smaller fragments returned in a change stream cursor.
 * You can only use $changeStreamSplitLargeEvent in a $changeStream pipeline and it must be the final stage in the pipeline.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStreamSplitLargeEvent/
 */
class ChangeStreamSplitLargeEventStage implements StageInterface
{
    public const NAME = '$changeStreamSplitLargeEvent';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
