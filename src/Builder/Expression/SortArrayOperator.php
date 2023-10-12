<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Sorts the elements of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/
 */
readonly class SortArrayOperator implements ResolvesToArray
{
    public const NAME = '$sortArray';
    public const ENCODE = Encode::Object;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $input The array to be sorted.
     * The result is null if the expression: is missing, evaluates to null, or evaluates to undefined
     * If the expression evaluates to any other non-array value, the document returns an error.
     */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param Document|Serializable|array|stdClass $sortBy The document specifies a sort ordering. */
    public Document|Serializable|stdClass|array $sortBy;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $input The array to be sorted.
     * The result is null if the expression: is missing, evaluates to null, or evaluates to undefined
     * If the expression evaluates to any other non-array value, the document returns an error.
     * @param Document|Serializable|array|stdClass $sortBy The document specifies a sort ordering.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Document|Serializable|stdClass|array $sortBy,
    ) {
        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        $this->sortBy = $sortBy;
    }
}
