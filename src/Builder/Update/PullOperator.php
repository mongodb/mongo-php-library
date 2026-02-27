<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Update;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

/**
 * Removes all array elements that match a specified value or condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/pull/
 * @internal
 */
final class PullOperator implements UpdateInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$pull';
    public const PROPERTIES = ['field' => 'field'];

    /** @var stdClass<DateTimeInterface|FieldQueryInterface|Type|array|bool|float|int|null|stdClass|string> $field */
    public readonly stdClass $field;

    /**
     * @param DateTimeInterface|FieldQueryInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public function __construct(
        DateTimeInterface|Type|FieldQueryInterface|stdClass|array|bool|float|int|null|string ...$field,
    ) {
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
