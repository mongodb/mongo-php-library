<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;
use function is_object;

/**
 * Performs a recursive search on a collection. To each output document, adds a new array field that contains the traversal results of the recursive search for that document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/
 */
readonly class GraphLookupStage implements StageInterface
{
    public const NAME = '$graphLookup';
    public const ENCODE = Encode::Object;

    /**
     * @param non-empty-string $from Target collection for the $graphLookup operation to search, recursively matching the connectFromField to the connectToField. The from collection must be in the same database as any other collections used in the operation.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     */
    public string $from;

    /** @param BSONArray|ExpressionInterface|PackedArray|Type|array|bool|float|int|non-empty-string|null|stdClass $startWith Expression that specifies the value of the connectFromField with which to start the recursive search. Optionally, startWith may be array of values, each of which is individually followed through the traversal process. */
    public PackedArray|Type|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $startWith;

    /** @param non-empty-string $connectFromField Field name whose value $graphLookup uses to recursively match against the connectToField of other documents in the collection. If the value is an array, each element is individually followed through the traversal process. */
    public string $connectFromField;

    /** @param non-empty-string $connectToField Field name in other documents against which to match the value of the field specified by the connectFromField parameter. */
    public string $connectToField;

    /** @param non-empty-string $as Name of the array field added to each output document. Contains the documents traversed in the $graphLookup stage to reach the document. */
    public string $as;

    /** @param Optional|int $maxDepth Non-negative integral number specifying the maximum recursion depth. */
    public Optional|int $maxDepth;

    /** @param Optional|non-empty-string $depthField Name of the field to add to each traversed document in the search path. The value of this field is the recursion depth for the document, represented as a NumberLong. Recursion depth value starts at zero, so the first lookup corresponds to zero depth. */
    public Optional|string $depthField;

    /** @param Optional|Document|QueryInterface|Serializable|array|stdClass $restrictSearchWithMatch A document specifying additional conditions for the recursive search. The syntax is identical to query filter syntax. */
    public Optional|Document|Serializable|QueryInterface|stdClass|array $restrictSearchWithMatch;

    /**
     * @param non-empty-string $from Target collection for the $graphLookup operation to search, recursively matching the connectFromField to the connectToField. The from collection must be in the same database as any other collections used in the operation.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param BSONArray|ExpressionInterface|PackedArray|Type|array|bool|float|int|non-empty-string|null|stdClass $startWith Expression that specifies the value of the connectFromField with which to start the recursive search. Optionally, startWith may be array of values, each of which is individually followed through the traversal process.
     * @param non-empty-string $connectFromField Field name whose value $graphLookup uses to recursively match against the connectToField of other documents in the collection. If the value is an array, each element is individually followed through the traversal process.
     * @param non-empty-string $connectToField Field name in other documents against which to match the value of the field specified by the connectFromField parameter.
     * @param non-empty-string $as Name of the array field added to each output document. Contains the documents traversed in the $graphLookup stage to reach the document.
     * @param Optional|int $maxDepth Non-negative integral number specifying the maximum recursion depth.
     * @param Optional|non-empty-string $depthField Name of the field to add to each traversed document in the search path. The value of this field is the recursion depth for the document, represented as a NumberLong. Recursion depth value starts at zero, so the first lookup corresponds to zero depth.
     * @param Optional|Document|QueryInterface|Serializable|array|stdClass $restrictSearchWithMatch A document specifying additional conditions for the recursive search. The syntax is identical to query filter syntax.
     */
    public function __construct(
        string $from,
        PackedArray|Type|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $startWith,
        string $connectFromField,
        string $connectToField,
        string $as,
        Optional|int $maxDepth = Optional::Undefined,
        Optional|string $depthField = Optional::Undefined,
        Optional|Document|Serializable|QueryInterface|stdClass|array $restrictSearchWithMatch = Optional::Undefined,
    ) {
        $this->from = $from;
        if (is_array($startWith) && ! array_is_list($startWith)) {
            throw new InvalidArgumentException('Expected $startWith argument to be a list, got an associative array.');
        }

        $this->startWith = $startWith;
        $this->connectFromField = $connectFromField;
        $this->connectToField = $connectToField;
        $this->as = $as;
        $this->maxDepth = $maxDepth;
        $this->depthField = $depthField;
        if (is_array($restrictSearchWithMatch) || is_object($restrictSearchWithMatch)) {
            $restrictSearchWithMatch = QueryObject::create($restrictSearchWithMatch);
        }

        $this->restrictSearchWithMatch = $restrictSearchWithMatch;
    }
}
