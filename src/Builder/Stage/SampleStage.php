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
 * Randomly selects the specified number of documents from its input.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sample/
 */
class SampleStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var int $size The number of documents to randomly select. */
    public readonly int $size;

    /**
     * @param int $size The number of documents to randomly select.
     */
    public function __construct(int $size)
    {
        $this->size = $size;
    }

    public function getOperator(): string
    {
        return '$sample';
    }
}
