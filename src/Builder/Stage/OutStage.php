<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Writes the resulting documents of the aggregation pipeline to a collection. To use the $out stage, it must be the last stage in the pipeline.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/
 */
class OutStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|Serializable|array|stdClass|string $coll Target database name to write documents from $out to. */
    public readonly Document|Serializable|stdClass|array|string $coll;

    /**
     * @param Document|Serializable|array|stdClass|string $coll Target database name to write documents from $out to.
     */
    public function __construct(Document|Serializable|stdClass|array|string $coll)
    {
        $this->coll = $coll;
    }

    public function getOperator(): string
    {
        return '$out';
    }
}
