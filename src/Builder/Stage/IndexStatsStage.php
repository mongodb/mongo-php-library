<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\InputStageInterface;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Returns statistics regarding the use of each index for the collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexStats/
 * @internal
 */
final class IndexStatsStage implements InputStageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$indexStats';

    public function __construct()
    {
    }
}
