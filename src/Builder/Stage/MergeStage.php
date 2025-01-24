<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Writes the resulting documents of the aggregation pipeline to a collection. The stage can incorporate (insert new documents, merge documents, replace documents, keep existing documents, fail the operation, process documents with a custom update pipeline) the results into an output collection. To use the $merge stage, it must be the last stage in the pipeline.
 * New in MongoDB 4.2.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/
 * @internal
 */
final class MergeStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$merge';

    public const PROPERTIES = [
        'into' => 'into',
        'on' => 'on',
        'let' => 'let',
        'whenMatched' => 'whenMatched',
        'whenNotMatched' => 'whenNotMatched',
    ];

    /** @var Document|Serializable|array|stdClass|string $into The output collection. */
    public readonly Document|Serializable|stdClass|array|string $into;

    /** @var Optional|BSONArray|PackedArray|array|string $on Field or fields that act as a unique identifier for a document. The identifier determines if a results document matches an existing document in the output collection. */
    public readonly Optional|PackedArray|BSONArray|array|string $on;

    /** @var Optional|Document|Serializable|array|stdClass $let Specifies variables for use in the whenMatched pipeline. */
    public readonly Optional|Document|Serializable|stdClass|array $let;

    /** @var Optional|BSONArray|PackedArray|Pipeline|array|string $whenMatched The behavior of $merge if a result document and an existing document in the collection have the same value for the specified on field(s). */
    public readonly Optional|PackedArray|Pipeline|BSONArray|array|string $whenMatched;

    /** @var Optional|string $whenNotMatched The behavior of $merge if a result document does not match an existing document in the out collection. */
    public readonly Optional|string $whenNotMatched;

    /**
     * @param Document|Serializable|array|stdClass|string $into The output collection.
     * @param Optional|BSONArray|PackedArray|array|string $on Field or fields that act as a unique identifier for a document. The identifier determines if a results document matches an existing document in the output collection.
     * @param Optional|Document|Serializable|array|stdClass $let Specifies variables for use in the whenMatched pipeline.
     * @param Optional|BSONArray|PackedArray|Pipeline|array|string $whenMatched The behavior of $merge if a result document and an existing document in the collection have the same value for the specified on field(s).
     * @param Optional|string $whenNotMatched The behavior of $merge if a result document does not match an existing document in the out collection.
     */
    public function __construct(
        Document|Serializable|stdClass|array|string $into,
        Optional|PackedArray|BSONArray|array|string $on = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $let = Optional::Undefined,
        Optional|PackedArray|Pipeline|BSONArray|array|string $whenMatched = Optional::Undefined,
        Optional|string $whenNotMatched = Optional::Undefined,
    ) {
        $this->into = $into;
        if (is_array($on) && ! array_is_list($on)) {
            throw new InvalidArgumentException('Expected $on argument to be a list, got an associative array.');
        }

        $this->on = $on;
        $this->let = $let;
        if (is_array($whenMatched) && ! array_is_list($whenMatched)) {
            throw new InvalidArgumentException('Expected $whenMatched argument to be a list, got an associative array.');
        }

        $this->whenMatched = $whenMatched;
        $this->whenNotMatched = $whenNotMatched;
    }
}
