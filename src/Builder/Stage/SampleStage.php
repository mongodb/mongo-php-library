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
 * @internal
 */
final class SampleStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$sample';
    public const PROPERTIES = ['size' => 'size'];

    /** @var int|string $size The number of documents to randomly select. */
    public readonly int|string $size;

    /**
     * @param int|string $size The number of documents to randomly select.
     */
    public function __construct(int|string $size)
    {
        $this->size = $size;
    }
}
