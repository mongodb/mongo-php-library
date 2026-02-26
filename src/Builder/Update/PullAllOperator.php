<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Update;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function is_string;

/**
 * Removes all matching values from an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/pullAll/
 * @internal
 */
final class PullAllOperator implements UpdateInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$pullAll';
    public const PROPERTIES = ['field' => 'field'];

    /** @var stdClass<BSONArray|PackedArray|array> $field */
    public readonly stdClass $field;

    /**
     * @param BSONArray|PackedArray|array ...$field
     */
    public function __construct(PackedArray|BSONArray|array ...$field)
    {
        if (\count($field) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }

        foreach($field as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $field arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $field = (object) $field;
        $this->field = $field;
    }
}
