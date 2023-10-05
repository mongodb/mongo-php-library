<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;
use stdClass;

class DensifyStage implements StageInterface
{
    public const NAME = '$densify';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param FieldPath|non-empty-string $field The field to densify. The values of the specified field must either be all numeric values or all dates.
     * Documents that do not contain the specified field continue through the pipeline unmodified.
     * To specify a <field> in an embedded document or in an array, use dot notation.
     */
    public FieldPath|string $field;

    /** @param array|stdClass $range Specification for range based densification. */
    public stdClass|array $range;

    /** @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $partitionByFields The field(s) that will be used as the partition keys. */
    public PackedArray|Optional|BSONArray|array $partitionByFields;

    /**
     * @param FieldPath|non-empty-string $field The field to densify. The values of the specified field must either be all numeric values or all dates.
     * Documents that do not contain the specified field continue through the pipeline unmodified.
     * To specify a <field> in an embedded document or in an array, use dot notation.
     * @param array|stdClass $range Specification for range based densification.
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $partitionByFields The field(s) that will be used as the partition keys.
     */
    public function __construct(
        FieldPath|string $field,
        stdClass|array $range,
        PackedArray|Optional|BSONArray|array $partitionByFields = Optional::Undefined,
    ) {
        $this->field = $field;
        $this->range = $range;
        if (\is_array($partitionByFields) && ! \array_is_list($partitionByFields)) {
            throw new \InvalidArgumentException('Expected $partitionByFields argument to be a list, got an associative array.');
        }
        $this->partitionByFields = $partitionByFields;
    }
}
