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
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Categorizes incoming documents into groups, called buckets, based on a specified expression and bucket boundaries.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucket/
 */
class BucketStage implements StageInterface
{
    public const NAME = '$bucket';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param ExpressionInterface|FieldPath|mixed|non-empty-string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * Unless $bucket includes a default specification, each input document must resolve the groupBy field path or expression to a value that falls within one of the ranges specified by the boundaries.
     */
    public mixed $groupBy;

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $boundaries An array of values based on the groupBy expression that specify the boundaries for each bucket. Each adjacent pair of values acts as the inclusive lower boundary and the exclusive upper boundary for the bucket. You must specify at least two boundaries.
     * The specified values must be in ascending order and all of the same type. The exception is if the values are of mixed numeric types, such as:
     */
    public PackedArray|BSONArray|array $boundaries;

    /**
     * @param ExpressionInterface|Optional|mixed $default A literal that specifies the _id of an additional bucket that contains all documents whose groupBy expression result does not fall into a bucket specified by boundaries.
     * If unspecified, each input document must resolve the groupBy expression to a value within one of the bucket ranges specified by boundaries or the operation throws an error.
     * The default value must be less than the lowest boundaries value, or greater than or equal to the highest boundaries value.
     * The default value can be of a different type than the entries in boundaries.
     */
    public mixed $default;

    /**
     * @param Document|Optional|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * If you do not specify an output document, the operation returns a count field containing the number of documents in each bucket.
     * If you specify an output document, only the fields specified in the document are returned; i.e. the count field is not returned unless it is explicitly included in the output document.
     */
    public Document|Serializable|Optional|stdClass|array $output;

    /**
     * @param ExpressionInterface|FieldPath|mixed|non-empty-string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * Unless $bucket includes a default specification, each input document must resolve the groupBy field path or expression to a value that falls within one of the ranges specified by the boundaries.
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $boundaries An array of values based on the groupBy expression that specify the boundaries for each bucket. Each adjacent pair of values acts as the inclusive lower boundary and the exclusive upper boundary for the bucket. You must specify at least two boundaries.
     * The specified values must be in ascending order and all of the same type. The exception is if the values are of mixed numeric types, such as:
     * @param ExpressionInterface|Optional|mixed $default A literal that specifies the _id of an additional bucket that contains all documents whose groupBy expression result does not fall into a bucket specified by boundaries.
     * If unspecified, each input document must resolve the groupBy expression to a value within one of the bucket ranges specified by boundaries or the operation throws an error.
     * The default value must be less than the lowest boundaries value, or greater than or equal to the highest boundaries value.
     * The default value can be of a different type than the entries in boundaries.
     * @param Document|Optional|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * If you do not specify an output document, the operation returns a count field containing the number of documents in each bucket.
     * If you specify an output document, only the fields specified in the document are returned; i.e. the count field is not returned unless it is explicitly included in the output document.
     */
    public function __construct(
        mixed $groupBy,
        PackedArray|BSONArray|array $boundaries,
        mixed $default = Optional::Undefined,
        Document|Serializable|Optional|stdClass|array $output = Optional::Undefined,
    ) {
        $this->groupBy = $groupBy;
        if (\is_array($boundaries) && ! \array_is_list($boundaries)) {
            throw new \InvalidArgumentException('Expected $boundaries argument to be a list, got an associative array.');
        }
        $this->boundaries = $boundaries;
        $this->default = $default;
        $this->output = $output;
    }
}
