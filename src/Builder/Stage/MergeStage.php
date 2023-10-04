<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;

class MergeStage implements StageInterface
{
    public const NAME = '$merge';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param array|non-empty-string|object $into The output collection. */
    public array|object|string $into;

    /** @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed>|non-empty-string $on Field or fields that act as a unique identifier for a document. The identifier determines if a results document matches an existing document in the output collection. */
    public PackedArray|Optional|BSONArray|array|string $on;

    /** @param Document|Optional|Serializable|array|object $let Specifies variables for use in the whenMatched pipeline. */
    public array|object $let;

    /** @param Optional|non-empty-string $whenMatched The behavior of $merge if a result document and an existing document in the collection have the same value for the specified on field(s). */
    public Optional|string $whenMatched;

    /** @param Optional|non-empty-string $whenNotMatched The behavior of $merge if a result document does not match an existing document in the out collection. */
    public Optional|string $whenNotMatched;

    /**
     * @param array|non-empty-string|object $into The output collection.
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed>|non-empty-string $on Field or fields that act as a unique identifier for a document. The identifier determines if a results document matches an existing document in the output collection.
     * @param Document|Optional|Serializable|array|object $let Specifies variables for use in the whenMatched pipeline.
     * @param Optional|non-empty-string $whenMatched The behavior of $merge if a result document and an existing document in the collection have the same value for the specified on field(s).
     * @param Optional|non-empty-string $whenNotMatched The behavior of $merge if a result document does not match an existing document in the out collection.
     */
    public function __construct(
        array|object|string $into,
        PackedArray|Optional|BSONArray|array|string $on = Optional::Undefined,
        array|object $let = Optional::Undefined,
        Optional|string $whenMatched = Optional::Undefined,
        Optional|string $whenNotMatched = Optional::Undefined,
    ) {
        $this->into = $into;
        if (\is_array($on) && ! \array_is_list($on)) {
            throw new \InvalidArgumentException('Expected $on argument to be a list, got an associative array.');
        }
        $this->on = $on;
        $this->let = $let;
        $this->whenMatched = $whenMatched;
        $this->whenNotMatched = $whenNotMatched;
    }
}
