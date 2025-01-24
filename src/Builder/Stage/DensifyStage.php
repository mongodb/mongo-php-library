<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
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
 * Creates new documents in a sequence of documents where certain values in a field are missing.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/densify/
 * @internal
 */
final class DensifyStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$densify';
    public const PROPERTIES = ['field' => 'field', 'range' => 'range', 'partitionByFields' => 'partitionByFields'];

    /**
     * @var string $field The field to densify. The values of the specified field must either be all numeric values or all dates.
     * Documents that do not contain the specified field continue through the pipeline unmodified.
     * To specify a <field> in an embedded document or in an array, use dot notation.
     */
    public readonly string $field;

    /** @var Document|Serializable|array|stdClass $range Specification for range based densification. */
    public readonly Document|Serializable|stdClass|array $range;

    /** @var Optional|BSONArray|PackedArray|array $partitionByFields The field(s) that will be used as the partition keys. */
    public readonly Optional|PackedArray|BSONArray|array $partitionByFields;

    /**
     * @param string $field The field to densify. The values of the specified field must either be all numeric values or all dates.
     * Documents that do not contain the specified field continue through the pipeline unmodified.
     * To specify a <field> in an embedded document or in an array, use dot notation.
     * @param Document|Serializable|array|stdClass $range Specification for range based densification.
     * @param Optional|BSONArray|PackedArray|array $partitionByFields The field(s) that will be used as the partition keys.
     */
    public function __construct(
        string $field,
        Document|Serializable|stdClass|array $range,
        Optional|PackedArray|BSONArray|array $partitionByFields = Optional::Undefined,
    ) {
        $this->field = $field;
        $this->range = $range;
        if (is_array($partitionByFields) && ! array_is_list($partitionByFields)) {
            throw new InvalidArgumentException('Expected $partitionByFields argument to be a list, got an associative array.');
        }

        $this->partitionByFields = $partitionByFields;
    }
}
