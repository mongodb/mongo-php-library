<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use DateTimeInterface;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Performs a recursive search on a collection. To each output document, adds a new array field that contains the traversal results of the recursive search for that document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/
 * @internal
 */
final class GraphLookupStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$graphLookup';

    public const PROPERTIES = [
        'from' => 'from',
        'startWith' => 'startWith',
        'connectFromField' => 'connectFromField',
        'connectToField' => 'connectToField',
        'as' => 'as',
        'maxDepth' => 'maxDepth',
        'depthField' => 'depthField',
        'restrictSearchWithMatch' => 'restrictSearchWithMatch',
    ];

    /**
     * @var string $from Target collection for the $graphLookup operation to search, recursively matching the connectFromField to the connectToField. The from collection must be in the same database as any other collections used in the operation.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     */
    public readonly string $from;

    /** @var BSONArray|DateTimeInterface|ExpressionInterface|PackedArray|Type|array|bool|float|int|null|stdClass|string $startWith Expression that specifies the value of the connectFromField with which to start the recursive search. Optionally, startWith may be array of values, each of which is individually followed through the traversal process. */
    public readonly DateTimeInterface|PackedArray|Type|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $startWith;

    /** @var string $connectFromField Field name whose value $graphLookup uses to recursively match against the connectToField of other documents in the collection. If the value is an array, each element is individually followed through the traversal process. */
    public readonly string $connectFromField;

    /** @var string $connectToField Field name in other documents against which to match the value of the field specified by the connectFromField parameter. */
    public readonly string $connectToField;

    /** @var string $as Name of the array field added to each output document. Contains the documents traversed in the $graphLookup stage to reach the document. */
    public readonly string $as;

    /** @var Optional|int $maxDepth Non-negative integral number specifying the maximum recursion depth. */
    public readonly Optional|int $maxDepth;

    /** @var Optional|string $depthField Name of the field to add to each traversed document in the search path. The value of this field is the recursion depth for the document, represented as a NumberLong. Recursion depth value starts at zero, so the first lookup corresponds to zero depth. */
    public readonly Optional|string $depthField;

    /** @var Optional|QueryInterface|array $restrictSearchWithMatch A document specifying additional conditions for the recursive search. The syntax is identical to query filter syntax. */
    public readonly Optional|QueryInterface|array $restrictSearchWithMatch;

    /**
     * @param string $from Target collection for the $graphLookup operation to search, recursively matching the connectFromField to the connectToField. The from collection must be in the same database as any other collections used in the operation.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param BSONArray|DateTimeInterface|ExpressionInterface|PackedArray|Type|array|bool|float|int|null|stdClass|string $startWith Expression that specifies the value of the connectFromField with which to start the recursive search. Optionally, startWith may be array of values, each of which is individually followed through the traversal process.
     * @param string $connectFromField Field name whose value $graphLookup uses to recursively match against the connectToField of other documents in the collection. If the value is an array, each element is individually followed through the traversal process.
     * @param string $connectToField Field name in other documents against which to match the value of the field specified by the connectFromField parameter.
     * @param string $as Name of the array field added to each output document. Contains the documents traversed in the $graphLookup stage to reach the document.
     * @param Optional|int $maxDepth Non-negative integral number specifying the maximum recursion depth.
     * @param Optional|string $depthField Name of the field to add to each traversed document in the search path. The value of this field is the recursion depth for the document, represented as a NumberLong. Recursion depth value starts at zero, so the first lookup corresponds to zero depth.
     * @param Optional|QueryInterface|array $restrictSearchWithMatch A document specifying additional conditions for the recursive search. The syntax is identical to query filter syntax.
     */
    public function __construct(
        string $from,
        DateTimeInterface|PackedArray|Type|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $startWith,
        string $connectFromField,
        string $connectToField,
        string $as,
        Optional|int $maxDepth = Optional::Undefined,
        Optional|string $depthField = Optional::Undefined,
        Optional|QueryInterface|array $restrictSearchWithMatch = Optional::Undefined,
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
        if (is_array($restrictSearchWithMatch)) {
            $restrictSearchWithMatch = QueryObject::create($restrictSearchWithMatch);
        }

        $this->restrictSearchWithMatch = $restrictSearchWithMatch;
    }
}
