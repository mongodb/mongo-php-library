<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Matches none of the values specified in an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/
 * @internal
 */
final class NinOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$nin';
    public const PROPERTIES = ['value' => 'value'];

    /** @var BSONArray|PackedArray|array $value */
    public readonly PackedArray|BSONArray|array $value;

    /**
     * @param BSONArray|PackedArray|array $value
     */
    public function __construct(PackedArray|BSONArray|array $value)
    {
        if (is_array($value) && ! array_is_list($value)) {
            throw new InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }

        $this->value = $value;
    }
}
