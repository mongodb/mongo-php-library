<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\OutputStageInterface;

/**
 * Splits large change stream events that exceed 16 MB into smaller fragments returned in a change stream cursor.
 * You can only use $changeStreamSplitLargeEvent in a $changeStream pipeline and it must be the final stage in the pipeline.
 *
 * New in MongoDB 6.1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStreamSplitLargeEvent/
 * @internal
 */
final class ChangeStreamSplitLargeEventStage implements OutputStageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$changeStreamSplitLargeEvent';

    public function __construct()
    {
    }
}
