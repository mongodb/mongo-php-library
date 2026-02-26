<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Update;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Increments a field by the specified numeric amount.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/inc/
 * @internal
 */
final class IncOperator implements UpdateInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$inc';
    public const PROPERTIES = ['field' => 'field'];

    /** @var stdClass<Decimal128|Int64|float|int> $field */
    public readonly stdClass $field;

    /**
     * @param Decimal128|Int64|float|int ...$field
     */
    public function __construct(Decimal128|Int64|float|int ...$field)
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
